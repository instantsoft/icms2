<?php

class actionAdminMenuItemDelete extends cmsAction {

    public function run($id = false) {

        if ($id) {
            $items = [$id];
        } else {
            $items = $this->request->get('selected', []);
        }

        if (!$items) {
            return cmsCore::error404();
        }

        if (!cmsForm::validateCSRFToken($this->request->get('csrf_token', ''))) {
            return cmsCore::error404();
        }

        foreach ($items as $item_id) {
            if (is_numeric($item_id)) {
                $this->model_menu->deleteMenuItem($item_id);
            }
        }

        cmsUser::addSessionMessage(LANG_DELETE_SUCCESS, 'success');

        return $this->redirectToAction('menu');
    }

}
