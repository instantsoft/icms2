<?php

class actionContentItemPropsFields extends cmsAction {

    public function run(){

        if (!$this->request->isAjax()) { cmsCore::error404(); }

        $ctype_name  = $this->request->get('ctype_name', '');
        $category_id = $this->request->get('category_id', 0);
        $item_id     = $this->request->get('item_id', 0);

        if (!$ctype_name || !$category_id) { cmsCore::error404(); }

        $ctype = $this->model->getContentTypeByName($ctype_name);
        if (!$ctype) { cmsCore::error404(); }

        $template = cmsTemplate::getInstance();

        $props = $this->model->getContentProps($ctype['name'], $category_id);

        if (!$props){
            $template->renderJSON(array(
                'success' => false,
                'html' => ''
            ));
        }


        $values = $item_id ? $this->model->getPropsValues($ctype['name'], $item_id) : array();

        $fields = $this->getPropsFields($props);

        $props_html = $template->render('item_props_fields', array(
            'props' => $props,
            'fields' => $fields,
            'values' => $values,
        ), new cmsRequest(array(), cmsRequest::CTX_INTERNAL));

        $template->renderJSON(array(
            'success' => true,
            'html' => $props_html
        ));

    }

}