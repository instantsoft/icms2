<?php

class actionAdminCtypesPropsReorder extends cmsAction {

    public function run($ctype_name){

        $items = $this->request->get('items');

        if (!$items || !$ctype_name){ cmsCore::error404(); }

        $content_model = cmsCore::getModel('content');

        $content_model->reorderContentProps($ctype_name, $items);

        $this->redirectBack();

    }

}
