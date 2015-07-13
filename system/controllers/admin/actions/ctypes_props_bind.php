<?php

class actionAdminCtypesPropsBind extends cmsAction {

    public function run($ctype_id, $category_id){

        $prop_id = $this->request->get('prop_id');
        $is_childs = $this->request->get('is_childs');

        if (!$prop_id) { $this->redirectBack(); }

        if (!$ctype_id || !$category_id) { cmsCore::error404(); }

        $content_model = cmsCore::getModel('content');

        $ctype = $content_model->getContentType($ctype_id);

        $cats = array($category_id);

        if ($is_childs){
            $subcats = $content_model->getSubCategoriesTree($ctype['name'], $category_id, false);
            if (is_array($subcats)) { foreach($subcats as $cat) { $cats[] = $cat['id']; } }
        }

        $content_model->bindContentProp($ctype['name'], $prop_id, $cats);

        $this->redirectToAction('ctypes', array('props', $ctype_id));

    }

}
