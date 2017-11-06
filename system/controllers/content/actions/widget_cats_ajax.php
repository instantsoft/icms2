<?php

class actionContentWidgetCatsAjax extends cmsAction {

    public function run(){

		if (!$this->request->isAjax()){ cmsCore::error404(); }
		if (!cmsUser::isAdmin()) { cmsCore::error404(); }

		$ctype_id = $this->request->get('value', 0);
		if (!$ctype_id) { cmsCore::error404(); }

		$ctype = $this->model->getContentType($ctype_id);

		if (!$ctype) { cmsCore::error404(); }

		$cats = $this->model->getCategoriesTree($ctype['name']);

		$cats_list = array();

		if ($cats){
			foreach($cats as $cat){

				if ($cat['ns_level'] > 1){
					$cat['title'] = str_repeat('-', $cat['ns_level']) . ' ' . $cat['title'];
				}

				$cats_list[$cat['id']] = $cat['title'];

			}
		}

		return $this->cms_template->renderJSON($cats_list);

    }

}
