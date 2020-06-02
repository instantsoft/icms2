<?php

class actionContentWidgetFieldsAjax extends cmsAction {

    public function run(){

		if (!$this->request->isAjax() || !cmsUser::isAdmin()){ return cmsCore::error404(); }

		$ctype_id = $this->request->get('value', 0);
		if (!$ctype_id) { return cmsCore::error404(); }

		$ctype = $this->model->getContentType($ctype_id);
		if (!$ctype) { return cmsCore::error404(); }

        $filter = $this->request->get('filter',[]);

		$fields = $this->model->getContentFields($ctype['name']);

        $fields = cmsEventsManager::hook('ctype_content_fields', $fields);

		$list = [];

		if ($fields){
			$list[] = ['title'=>'', 'value'=>''];
			foreach($fields as $field){
                $is_continue = false;
                if($filter){
                    foreach ($filter as $key => $value) {
                        if(!is_array($value)){
                            if(isset($field[$key]) && $field[$key] == $value){
                                $is_continue = true;
                                continue;
                            }
                        } else {
                            foreach ($value as $_key => $_value) {
                                if(!$_value && empty($field[$key][$_key])){
                                    $is_continue = true;
                                    continue;
                                }
                                if(isset($field[$key][$_key]) && $field[$key][$_key] == $_value){
                                    $is_continue = true;
                                    continue;
                                }
                            }
                        }
                    }
                }
                if($is_continue){
                    continue;
                }
				$list[] = ['title'=>$field['title'], 'value'=>$field['name']];
			}
		}

		return $this->cms_template->renderJSON($list);

    }

}
