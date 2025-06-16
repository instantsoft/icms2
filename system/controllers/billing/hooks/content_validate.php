<?php
/**
 * Проверяем, хватит ли баланса
 *
 * @property \modelBilling $model
 */
class onBillingContentValidate extends cmsAction {

    private $fields_errors = [];

    public function run($data) {

        if ($this->cms_user->is_admin) {
            return $data;
        }

        list($item, $errors) = $data;

        // Уже есть ошибки
        if ($errors) {
            return $data;
        }

        // Стратуем транзакцию в этом хуке, завершаем в
        // content_after_add/content_after_update или
        // content_after_add_approve/content_after_update_approve
        $this->model->startTransaction();

        // Баланс
        $balance = $this->model->forUpdate()->getUserBalance($this->cms_user->id);

        $total_price = 0; $pub_days = 0;

        if (!$this->request->has('to_draft')) {

            // Если вдруг есть цена за добавление
            [$price_add, $action] = $this->getPriceAndAction('content', "{$item['ctype_name']}_add", $this->cms_user->id);

            $pub_days = !empty($item['pub_days']) ? (int) $item['pub_days'] : 0;

            // Цена за дни публикации
            $pub_days_price = $this->getTermDayPrice($item['ctype_data'], $this->cms_user->id, $pub_days);

            // Цена за заполненные поля
            $all_fields_price = $this->validateFields($item);

            $total_price = $price_add + $pub_days_price + $all_fields_price;
        }

        if ($total_price > $balance) {

            // В штатной ситуации цена за добавление уже проверена,
            // поэтому проверяем остальное

            if ($pub_days_price > 0) {
                $errors['pub_days'] = sprintf(LANG_BILLING_TERM_LOW_BALANCE, href_to('billing', 'deposit') . "?amount={$pub_days_price}");
            }

            foreach ($this->fields_errors as $name => $price_value) {
                $errors[$name] = sprintf(LANG_BILLING_TERM_LOW_BALANCE, href_to('billing', 'deposit') . "?amount={$price_value}");
            }

        } else {

            // Для хуков content_after_add/content_after_update
            cmsModel::cacheResult('billing_item_price', $total_price);
            cmsModel::cacheResult('billing_item_pub_days', $pub_days);
        }

        return [$item, $errors];
    }

    private function validateFields($item) {

        $fields = $this->model->getContentTypeVipFields($item['ctype_id']);
        if (!$fields) {
            return 0;
        }

        $summ = 0;

        foreach ($fields as $field) {

            if (is_empty_value($item[$field['field']] ?? false)) {
                continue;
            }

            $is_paid = !empty($item['id']) ? $this->model->isVipFieldPurchased($this->cms_user->id, $field['id'], $item['id']) : false;
            if ($is_paid) {
                continue;
            }

            $price = $this->getPriceForUser($field['prices']);

            if ($price > 0) {
                $this->fields_errors[$field['field']] = $price;
            }

            $summ += $price;
        }

        return $summ;
    }

}
