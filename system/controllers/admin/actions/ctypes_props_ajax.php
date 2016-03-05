<?php

class actionAdminCtypesPropsAjax extends cmsAction {

    public function run($ctype_name, $category_id=false){

        if (!$this->request->isAjax()) { cmsCore::error404(); }

        if (!$ctype_name) { cmsCore::error404(); }

        $grid = $this->loadDataGrid('ctype_props');

        $content_model = cmsCore::getModel('content');

        $content_model->orderBy('ordering', 'asc');

        $fields = $content_model->getContentPropsBinds($ctype_name, $category_id);

        cmsTemplate::getInstance()->renderGridRowsJSON($grid, $fields);

        $this->halt();

    }

}
