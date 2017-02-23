<?php

class actionManifestsReorder extends cmsAction {

    public function run(){

        $items = $this->request->get('items', array());
        if (!$items){ cmsCore::error404(); }

        $this->model->reorderManifests($items);

        cmsUser::addSessionMessage(LANG_CP_ORDER_SUCCESS, 'success');

        $this->redirectBack();

    }

}