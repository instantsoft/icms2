<?php

class actionAdminCtypesPropsDelete extends cmsAction {

    public function run($ctype_id, $prop_id){

        if (!$ctype_id || !$prop_id) { cmsCore::error404(); }

        $content_model = cmsCore::getModel('content');

        $content_model->deleteContentProp($ctype_id, $prop_id);

        cmsUser::addSessionMessage(LANG_DELETE_SUCCESS, 'success');

        $this->redirectToAction('ctypes', array('props', $ctype_id));

    }

}
