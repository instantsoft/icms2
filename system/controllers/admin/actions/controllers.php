<?php

class actionAdminControllers extends cmsAction {

    public function run($do = false) {

        // если нужно, передаем управление другому экшену
        if ($do){
            $this->runExternalAction('controllers_'.$do, array_slice($this->params, 1));
            return;
        }

        $grid = $this->loadDataGrid('controllers', false, 'admin.grid_filter.controllers');

        return $this->cms_template->render('controllers', array(
            'grid' => $grid
        ));

    }

}
