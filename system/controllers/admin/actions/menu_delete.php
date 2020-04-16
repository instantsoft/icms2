<?php

class actionAdminMenuDelete extends cmsAction {

    public function run($id){

        if (!$id) { cmsCore::error404(); }

        $menu_model = cmsCore::getModel('menu');

        $menu = $menu_model->getMenu($id);

        if ($menu['is_fixed']) {
            cmsUser::addSessionMessage(LANG_CP_MENU_IS_FIXED);
            $this->redirectBack();
        }

        $menu_model->deleteMenu($id);

        cmsUser::setCookiePublic('menu_tree_path', '1.0');

        $this->redirectToAction('menu');

    }

}
