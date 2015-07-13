<?php

class actionAdminCtypesPropsUnbind extends cmsAction {

    public function run($ctype_id, $prop_id, $cat_id){

        if (!$ctype_id || !$prop_id || !$cat_id) { cmsCore::error404(); }

        $content_model = cmsCore::getModel('content');

        $ctype = $content_model->getContentType($ctype_id);

        $content_model->unbindContentProp($ctype['name'], $prop_id, $cat_id);

        $this->redirectToAction('ctypes', array('props', $ctype_id));

    }

}
