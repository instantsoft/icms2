<?php

class actionUsersTabsReorder extends cmsAction {

    public function run(){

        $items = $this->request->get('items');
        if (!$items){ cmsCore::error404(); }

        $this->model->reorderUsersProfilesTabs($items);

        $this->redirectBack();

    }

}