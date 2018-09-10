<?php

class actionAdminMenu extends cmsAction {

    public function run($do = false) {

        // если нужно, передаем управление другому экшену
        if ($do){
            $this->runExternalAction('menu_'.$do, array_slice($this->params, 1));
            return;
        }

        $menu_model = cmsCore::getModel('menu');

        $menus = $menu_model->getMenus();

        $grid = $this->loadDataGrid('menu_items');

        return $this->cms_template->render('menu', array(
            'menus' => $menus,
            'grid' => $grid
        ));

    }

}
