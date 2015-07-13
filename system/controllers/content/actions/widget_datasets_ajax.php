<?php

class actionContentWidgetDatasetsAjax extends cmsAction {

    public function run(){

		if (!$this->request->isAjax()){ cmsCore::error404(); }
		if (!cmsUser::isAdmin()) { cmsCore::error404(); }
		
		$ctype_id = $this->request->get('value');
		
		if (!$ctype_id) { cmsCore::error404(); }
		
		$datasets = $this->model->getContentDatasets($ctype_id);
		
		$list = array();
		
		if ($datasets){		
			$list = array('0'=>'') + array_collection_to_list($datasets, 'id', 'title');
		}
		
		cmsTemplate::getInstance()->renderJSON($list);
		
    }

}
