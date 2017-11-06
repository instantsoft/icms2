<?php

class actionAdminCtypesDatasetsReorder extends cmsAction {

    public function run(){

        $items = $this->request->get('items', array());
        if (!$items){ cmsCore::error404(); }

        cmsCore::getModel('content')->reorderContentDatasets($items);

        cmsUser::addSessionMessage(LANG_CP_ORDER_SUCCESS, 'success');

        $this->redirectBack();

    }

}
