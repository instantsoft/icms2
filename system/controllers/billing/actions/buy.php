<?php
/**
 * @property \modelUsers $model_users
 * @property \modelContent $model_content
 * @property \modelBilling $model
 * @property \messages $controller_messages
 */
class actionBillingBuy extends cmsAction {

    public function run($paid_field_id, $item_id) {

        if (!$this->cms_user->is_logged) {
            return $this->redirectToLogin();
        }

        $paid_field = $this->model->getPaidField($paid_field_id);
        if (!$paid_field) {
            return cmsCore::error404();
        }

        $ctype = $this->model_content->getContentType($paid_field['ctype_id']);
        if (!$ctype) {
            return cmsCore::error404();
        }

        $item = $this->model_content->getContentItem($ctype['name'], $item_id);
        if (!$item) {
            return cmsCore::error404();
        }

        $item_url = href_to($ctype['name'], $item['slug']) . '.html';

        // Оплачено?
        if ($this->model->isPaidFieldPurchased($this->cms_user->id, $paid_field_id, $item['id'])) {
            return $this->redirect($item_url);
        }

        $price = $this->getPaidFieldPrice($paid_field, $item);

        $description_text = sprintf(LANG_BILLING_BUY_TICKET_TITLE, $item['title']);

        $this->model->startTransaction();

        $balance = $this->model->forUpdate()->getUserBalance($this->cms_user->id);

        if ($price > $balance) {

            cmsUser::sessionSet('billing_ticket', [
                'title'       => $description_text,
                'amount'      => $price,
                'diff_amount' => round($price - $balance, 2),
                'back_url'    => href_to($this->name, $this->current_action, $this->params)
            ]);

            return $this->redirectTo('billing', 'deposit');
        }

        if (!$this->request->has('submit')) {

            return $this->cms_template->render('buy', [
                'b_spellcount' => $this->options['currency'],
                'ctype'        => $ctype,
                'item'         => $item,
                'item_url'     => $item_url,
                'paid_field'   => $paid_field,
                'balance'      => $balance,
                'price'        => $price
            ]);
        }

        if (!cmsForm::validateCSRFToken($this->request->get('csrf_token', ''))) {

            cmsUser::addSessionMessage(LANG_FORM_ERRORS, 'error');

            return $this->redirectToAction($this->current_action, $this->params);
        }

        $description = [
            'text' => $description_text,
            'url'  => $item_url
        ];

        $success = $this->model->setPaidFieldPurchased($this->cms_user->id, $paid_field_id, $item['id']);

        if ($paid_field['is_to_author']) {

            $from_desc = $description;
            $to_desc   = [
                'text' => sprintf(LANG_BILLING_BUY_BY_USER, $item['title'], $this->cms_user->nickname),
                'url'  => href_to_profile($this->cms_user)
            ];

            $success = $success && $this->transfer($price, $this->cms_user->id, $item['user_id'], $from_desc, $to_desc);

        } else {

            $success = $success && $this->model->decrementUserBalance($this->cms_user->id, $price, $description);
        }

        $this->model->endTransaction($success);

        if (!$success) {

            cmsUser::addSessionMessage(LANG_BILLING_ERROR_TRY, 'error');

            return $this->redirectToAction($this->current_action, $this->params);
        }

        if ($paid_field['is_notify_author'] || $paid_field['notify_email']) {

            $letter = ['name' => 'billing_field_purchase'];

            $letter_data = [
                'buyer_url'  => href_to_profile($this->cms_user, false, true),
                'buyer_name' => $this->cms_user->nickname,
                'item_url'   => href_to_abs($item_url),
                'item_title' => $item['title']
            ];

            if ($paid_field['notify_email']) {

                $to = ['email' => $paid_field['notify_email']];

                $this->controller_messages->sendEmail($to, $letter, $letter_data);
            }

            if ($paid_field['is_notify_author']) {

                $author = $this->model_users->getUser($item['user_id']);

                if ($author) {

                    $to = ['email' => $author['email'], 'name' => $author['nickname']];

                    $this->controller_messages->sendEmail($to, $letter, $letter_data);
                }
            }
        }

        cmsUser::addSessionMessage(LANG_BILLING_BUY_SUCCESS, 'success');

        return $this->redirect($item_url);
    }

}
