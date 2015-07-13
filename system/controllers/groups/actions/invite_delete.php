<?php

class actionGroupsInviteDelete extends cmsAction {

    public function run($group_id, $invited_id){

        if (!$this->request->isInternal()){ cmsCore::error404(); }

        $invite = $this->model->getInvite($group_id, $invited_id);

        if ($invite){ $this->model->deleteInvite($invite['id']); }

        return true;

    }

}
