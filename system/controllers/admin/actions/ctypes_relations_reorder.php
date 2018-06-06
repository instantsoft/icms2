<?php

class actionAdminCtypesRelationsReorder extends cmsAction {

    public function run(){

        $items = $this->request->get('items', array());
        if (!$items){ cmsCore::error404(); }

        cmsCore::getModel('content')->reorderContentRelation($items);

        cmsUser::addSessionMessage(LANG_CP_ORDER_SUCCESS, 'success');

        $this->redirectBack();

    }

}
