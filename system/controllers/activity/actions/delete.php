<?php

class actionActivityDelete extends cmsAction {

    public function run($id) {

        if (!cmsUser::isAllowed('activity', 'delete')) {
            return cmsCore::error404();
        }

        if (!cmsForm::validateCSRFToken($this->request->get('csrf_token', ''))) {
            return cmsCore::error404();
        }

        $this->model->deleteEntryById($id);

        cmsEventsManager::hook('activity_after_delete', $id);

        cmsUser::addSessionMessage(LANG_DELETE_SUCCESS, 'success');

        return $this->redirectBack();
    }

}
