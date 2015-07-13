<?php

class actionAdminCtypesDatasetsReorder extends cmsAction {

    public function run(){

        $items = $this->request->get('items');

        if (!$items){ cmsCore::error404(); }

        $content_model = cmsCore::getModel('content');

        $content_model->reorderContentDatasets($items);

        $this->redirectBack();

    }

}
