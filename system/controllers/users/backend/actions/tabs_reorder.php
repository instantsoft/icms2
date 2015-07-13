<?php

class actionUsersTabsReorder extends cmsAction {

    public function run(){

        $items = $this->request->get('items');

        if (!$items){ cmsCore::error404(); }

        $users_model = cmsCore::getModel('users');

        $users_model->reorderUsersProfilesTabs($items);

        $this->redirectBack();

    }

}
