<?php

class actionContentWidgetRelationsAjax extends cmsAction {

    public function run(){

		if (!$this->request->isAjax()){ cmsCore::error404(); }
		if (!cmsUser::isAdmin()) { cmsCore::error404(); }

		$ctype_id = $this->request->get('value', 0);
		if (!$ctype_id) { cmsCore::error404(); }

        $ctype = $this->model->getContentType($ctype_id);
        if (!$ctype) { cmsCore::error404(); }

        $parents = $this->model->getContentTypeParents($ctype_id);

		$list = array('0'=>'');

        if ($parents) {
            foreach($parents as $parent){
                $list[$parent['id']] = "{$ctype['title']} > {$parent['ctype_title']}";
            };
        }

		return $this->cms_template->renderJSON($list);

    }

}
