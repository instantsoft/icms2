<?php

class actionActivityDelete extends cmsAction{

    public function run($id = false){

        if (!$id) { cmsCore::error404(); }

        if (!cmsUser::isAllowed('activity', 'delete')){ cmsCore::error404(); }

        $this->model->deleteEntryById($id);

        cmsEventsManager::hook('activity_after_delete', $id);

        cmsUser::addSessionMessage(LANG_DELETE_SUCCESS, 'success');

        $this->redirectBack();

    }

}
