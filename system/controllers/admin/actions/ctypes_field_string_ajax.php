<?php

class actionAdminCtypesFieldStringAjax extends cmsAction {

    public function run($ctype_id = 0, $field_id = 0){

        if( !$this->request->isAjax()
                ||
            !is_numeric($ctype_id)
                ||
            !is_numeric($field_id)
        ){return cmsCore::error404();}

        $content_model = cmsCore::getModel('content');

        if( !($ctype = $content_model->getContentType($ctype_id))
                ||
            !($field = $content_model->getContentField($ctype['name'], $field_id))
        ){return cmsCore::error404();}

        $content_model->selectOnly('COUNT(i.id)', 'stroki')->
                select('i.'.$field['name'], 'field')->
                groupBy('i.'.$field['name'])->
                order_by = 'stroki DESC';

        $items = $content_model->get($content_model->table_prefix.$ctype['name'], function($item){
            return $item['field'];
        });

        $this->cms_template->renderJSON(array(
			'error' => false,
            'result'=> implode("\n", array_diff($items, array('')))
		));

    }

}
