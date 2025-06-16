<?php
/**
 * @property \modelBilling $model
 * @property \modelAdmin $model_admin
 * @property \modelUsers $model_users
 */
class actionBillingPrices extends cmsAction {

    public function run($do = false, $id = false) {

        if ($do) {
            return $this->runAction('prices_' . $do, array_slice($this->params, 1));
        }

        $groups      = $this->model_users->getGroups();
        $actions     = $this->model->getActions();
        $controllers = $this->model_admin->getInstalledControllers();

        if ($this->request->has('submit')) {

            $csrf_token = $this->request->get('csrf_token', '');
            if (!cmsForm::validateCSRFToken($csrf_token)) {

                cmsUser::addSessionMessage(LANG_FORM_ERRORS, 'error');

                return $this->redirectBack();
            }

            $all_titles = $this->request->get('titles', []);
            $all_prices = $this->request->get('prices', []);

            foreach ($all_titles as $action_id => $title) {

                if (!is_numeric($action_id) || is_array($title) ||
                        empty($all_prices[$action_id]) || !is_array($all_prices[$action_id])) {
                    return cmsCore::error404();
                }

                $prices = [];

                foreach ($all_prices[$action_id] as $idx => $price) {

                    if (!is_numeric($idx) || is_array($price)) {
                        return cmsCore::error404();
                    }

                    $price = round(trim(str_replace(',', '.', $price)), 2);

                    $prices[$idx] = $price;
                }

                $action = [
                    'title'  => $title,
                    'prices' => $prices
                ];

                $this->model->updateAction($action_id, $action);
            }

            cmsUser::addSessionMessage(LANG_SUCCESS_MSG, 'success');

            $this->redirectToAction('prices');
        }

        return $this->cms_template->render('backend/prices', [
            'actions'            => $actions,
            'groups'             => $groups,
            'controllers_titles' => array_collection_to_list($controllers, 'name', 'title')
        ]);
    }

}
