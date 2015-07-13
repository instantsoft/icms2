<?php

class actionContentWidgetFieldsAjax extends cmsAction {

    public function run(){

		if (!$this->request->isAjax()){ cmsCore::error404(); }
		if (!cmsUser::isAdmin()) { cmsCore::error404(); }
		
		$ctype_id = $this->request->get('value');
		
		if (!$ctype_id) { cmsCore::error404(); }
		
		$ctype = $this->model->getContentType($ctype_id);
		
		if (!$ctype) { cmsCore::error404(); }
		
		$fields = $this->model->getContentFields($ctype['name']);
		
		$list = array();
		
		if ($fields){		
			$list = array(''=>'') + array_collection_to_list($fields, 'name', 'title');
		}
		
		cmsTemplate::getInstance()->renderJSON($list);
		
    }

}
