<?php
/**
 * @property \modelMenu $model_menu
 */
class actionAdminMenuDelete extends cmsAction {

    public function run($id = false) {

        if (!$id) {
            return cmsCore::error404();
        }

        if (!cmsForm::validateCSRFToken($this->request->get('csrf_token', ''))) {
            return cmsCore::error404();
        }

        $menu = $this->model_menu->getMenu($id);

        if (!$menu) {
            return cmsCore::error404();
        }

        if ($menu['is_fixed']) {

            cmsUser::addSessionMessage(LANG_CP_MENU_IS_FIXED);

            return $this->redirectToAction('menu');
        }

        $this->model_menu->deleteMenu($id);

        cmsUser::setCookiePublic('menu_tree_path', '1.0');

        cmsUser::addSessionMessage(LANG_DELETE_SUCCESS, 'success');

        return $this->redirectToAction('menu');
    }

}
