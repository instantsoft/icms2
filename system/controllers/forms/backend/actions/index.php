<?php

class actionFormsIndex extends cmsAction {

    public function run($do = false) {

        // если нужно, передаем управление другому экшену
        if ($do){
            $this->runExternalAction($do, array_slice($this->params, 1));
            return;
        }

		$grid = $this->loadDataGrid('forms');

        if($this->request->isAjax()){

            $this->model->setPerPage(admin::perpage);

            $filter     = [];
            $filter_str = $this->request->get('filter', '');

            if ($filter_str){
                parse_str($filter_str, $filter);
                $this->model->applyGridFilter($grid, $filter);
            }

            $total = $this->model->getCount('forms');
            $perpage = isset($filter['perpage']) ? $filter['perpage'] : admin::perpage;
            $pages   = ceil($total / $perpage);

            $forms = $this->model->get('forms');

            $this->cms_template->renderGridRowsJSON($grid, $forms, $total, $pages);

            $this->halt();

        }

        return $this->cms_template->render([
            'grid' => $grid
        ]);

    }

}
