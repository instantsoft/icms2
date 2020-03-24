<?php

class actionAdminWidgetsPageContentCats extends cmsAction {

    public function run(){

        if( !$this->request->isAjax()
                ||
            !($ctype_name = $this->request->get('value', ''))
        ){ cmsCore::error404(); }

        $content_model = cmsCore::getModel('content');
        $ctype = $content_model->getContentTypeByName($ctype_name);
        if(!$ctype){ cmsCore::error404(); }

        $tree = $content_model->limit(0)->getCategoriesTree($ctype['name'], false)?:array();

        $items = array();

        $ctype_default = cmsConfig::get('ctype_default');

        foreach($tree as $item){
            $items[((!in_array($ctype['name'], $ctype_default) ? $ctype['name'].'/' : '').$item['slug'])] = str_repeat('- ', $item['ns_level']).' '.$item['title'];
        }

        return $this->cms_template->renderJSON($items);

    }

}
