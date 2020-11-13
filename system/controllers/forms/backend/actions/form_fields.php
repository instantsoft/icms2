<?php

class actionFormsFormFields extends cmsAction {

    public function run($form_id = 0) {

        $form_data = $this->model->getForm($form_id);
        if(!$form_data){ cmsCore::error404(); }

		$grid = $this->loadDataGrid('form_fields');

        if($this->request->isAjax()){

            $filter     = array();
            $filter_str = $this->request->get('filter', '');

            if ($filter_str){
                parse_str($filter_str, $filter);
                $this->model->applyGridFilter($grid, $filter);
            }

            $this->model->filterEqual('form_id', $form_id);

            $this->model->orderBy('ordering', 'asc');

            $fields = $this->model->getFormFields(false);

            $this->cms_template->renderGridRowsJSON($grid, $fields);

            $this->halt();

        }

        return $this->cms_template->render([
            'menu' => $this->getFormMenu('edit', $form_data['id']),
            'form_data' => $form_data,
            'grid' => $grid
        ]);

    }

}
