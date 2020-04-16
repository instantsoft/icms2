<?php

class actionContentWidgetRelationsAjax extends cmsAction {

    public function run(){

		if (!$this->request->isAjax() || !cmsUser::isAdmin()){ return cmsCore::error404(); }

		$ctype_id = $this->request->get('value', 0);
		if (!$ctype_id) { return cmsCore::error404(); }

        $ctype = $this->model->getContentType($ctype_id);
        if (!$ctype) { return cmsCore::error404(); }

        $parents = $this->model->getContentTypeParents($ctype_id);

		$list = array('0'=>'');

        if ($parents) {
            foreach($parents as $parent){
                $list[] = ['title'=>$ctype['title'].' > '.$parent['ctype_title'], 'value'=>$parent['id']];
            }
        }

		return $this->cms_template->renderJSON($list);

    }

}
