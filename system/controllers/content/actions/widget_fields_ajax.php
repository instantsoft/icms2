<?php

class actionContentWidgetFieldsAjax extends cmsAction {

    public function run(){

		if (!$this->request->isAjax() || !cmsUser::isAdmin()){ return cmsCore::error404(); }

		$ctype_id = $this->request->get('value', 0);
		if (!$ctype_id) { return cmsCore::error404(); }

		$ctype = $this->model->getContentType($ctype_id);
		if (!$ctype) { return cmsCore::error404(); }

		$fields = $this->model->getContentFields($ctype['name']);

        $fields = cmsEventsManager::hook('ctype_content_fields', $fields);

		$list = array();

		if ($fields){
			$list[] = ['title'=>'', 'value'=>''];
			foreach($fields as $field){
				$list[] = ['title'=>$field['title'], 'value'=>$field['name']];
			}
		}

		return $this->cms_template->renderJSON($list);

    }

}
