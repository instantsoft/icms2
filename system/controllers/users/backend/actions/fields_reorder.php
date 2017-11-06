<?php

class actionUsersFieldsReorder extends cmsAction {

    public function run(){

        $items = $this->request->get('items', array());
        if (!$items){ cmsCore::error404(); }

        $content_model = cmsCore::getModel('content');

        $content_model->setTablePrefix('');

        $content_model->reorderContentFields('{users}', $items);

        cmsUser::addSessionMessage(LANG_CP_ORDER_SUCCESS, 'success');

        $this->redirectBack();

    }

}
