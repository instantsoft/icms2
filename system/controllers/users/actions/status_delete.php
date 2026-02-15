<?php

class actionUsersStatusDelete extends cmsAction {

    public function run($user_id) {

        if (!$this->cms_user->is_logged || !$this->request->isAjax()) {
            return cmsCore::error404();
        }

        if (!cmsForm::validateCSRFToken($this->request->get('csrf_token', ''))) {

            return $this->cms_template->renderJSON(['error' => true, 'message' => LANG_ERROR]);
        }

        if ($this->cms_user->id != $user_id && !$this->cms_user->is_admin) {

            return $this->cms_template->renderJSON(['error' => true, 'message' => LANG_ERROR]);
        }

        $this->model->clearUserStatus($user_id);

        return $this->cms_template->renderJSON([
            'error' => false
        ]);
    }

}
