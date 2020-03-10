<?php

class actionAdminContentItemTrashPut extends cmsAction {

    public function run($ctype_id){

        $items = $this->request->get('selected', array());
        if (!$items) { cmsCore::error404(); }

        if (!cmsForm::validateCSRFToken( $this->request->get('csrf_token', '') )){
            cmsCore::error404();
        }

        $content_model = cmsCore::getModel('content');

        $ctype = $content_model->getContentType($ctype_id);
        if (!$ctype) { return cmsCore::error404(); }

        foreach($items as $id){

            $content_model->toTrashContentItem($ctype['name'], $id);

        }

        $this->redirectBack();

    }

}
