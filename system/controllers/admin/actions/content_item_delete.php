<?php

class actionAdminContentItemDelete extends cmsAction {

    public function run($ctype_id){

        $items = $this->request->get('selected');
        
        if (!$items) { cmsCore::error404(); }
        
        $content_model = cmsCore::getModel('content');
        
        $ctype = $content_model->getContentType($ctype_id);
        
        foreach($items as $id){
            $content_model->deleteContentItem($ctype['name'], $id);
        }
        
        $this->redirectBack();

    }

}
