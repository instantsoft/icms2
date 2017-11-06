<?php

class actionContentWidgetDatasetsAjax extends cmsAction {

    public function run(){

		if (!$this->request->isAjax()){ cmsCore::error404(); }
		if (!cmsUser::isAdmin()) { cmsCore::error404(); }

		$ctype_id = $this->request->get('value', '');
		if (!$ctype_id) { cmsCore::error404(); }

        $target_controller = 'content';

        if(strpos($ctype_id, ':') !== false){
            list($target_controller, $ctype_id) = explode(':', $ctype_id);
        }

		$datasets = $this->model->getContentDatasets($target_controller == 'content' ? $ctype_id : $target_controller);

		$list = array();

		if ($datasets){
			$list = array('0'=>'') + array_collection_to_list($datasets, 'id', 'title');
		}

		return $this->cms_template->renderJSON($list);

    }

}
