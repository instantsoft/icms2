<?php

class actionAdminCtypes extends cmsAction {

    public function run($do = false) {

        $this->model_content->reloadAllCtypes(false);

        // если нужно, передаем управление другому экшену
        if ($do){
            $this->runExternalAction('ctypes_'.$do, array_slice($this->params, 1));
            return;
        }

        $grid = $this->loadDataGrid('ctypes', false, 'admin.grid_filter.ctypes');

        $new_filter = array(); // далее сброс всех ранее сохранённых фильтров
        $new_filter['page'] = isset($grid['filter']['page']) ? $grid['filter']['page'] : 1;
        $new_filter['perpage'] = isset($grid['filter']['perpage']) ? $grid['filter']['perpage'] : 30;
        $grid['filter'] = $new_filter;

        return $this->cms_template->render('ctypes', array(
            'grid' => $grid
        ));

    }

}
