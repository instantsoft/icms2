<?php

class actionAdminWidgetsPageAutocomplete extends cmsAction {

    public function run(){

        if( !$this->request->isAjax()
                ||
            !($term = $this->request->get('term', ''))
                ||
            !($ctype_name = $this->request->get('ctype', ''))
        ){ cmsCore::error404(); }


        $content_model = cmsCore::getModel('content');
        $ctype = $content_model->getContentTypeByName($ctype_name);
        if(!$ctype){cmsCore::error404();}

        $content_model->filterStart()->filterLike('title', "{$term}%")->filterOr()->filterLike('slug', "{$term}%")->filterEnd();

        $items = $content_model->get($content_model->table_prefix.$ctype['name'])?:array();

        $result = array();

        $ctype_default = cmsConfig::get('ctype_default');

        foreach($items as $item){

            $result[] = array(
                'id'    => $item['id'],
                'label' => $item['title'],
                'value' => (!in_array($ctype['name'], $ctype_default) ? $ctype['name'].'/' : '').$item['slug'].'.html'
            );

        }

        return $this->cms_template->renderJSON($result);

    }

}
