<?php
/**
 * @property \modelBackendContent $model_backend_content
 * @property \modelUsers $model_users
 * @property \modelModeration $model_moderation
 */
class actionAdminCtypesModerators extends cmsAction {

    public function run($ctype_id = null, $action = 'view') {

        if (!$ctype_id) {
            return cmsCore::error404();
        }

        $this->ctype = $this->model_backend_content->getContentType($ctype_id);
        if (!$this->ctype) {
            return cmsCore::error404();
        }

        switch ($action) {

            case 'view': $this->view();
                break;
            case 'add': $this->add();
                break;
            case 'delete': $this->delete();
                break;

            default : cmsCore::error404();
        }

        return;
    }

    private function view() {

        $this->dispatchEvent('ctype_loaded', [$this->ctype, 'moderators']);

        $moderators = $this->model_moderation->getContentTypeModerators($this->ctype['name']);

        return $this->cms_template->render('ctypes_moderators', [
            'ctype'      => $this->ctype,
            'moderators' => $moderators
        ]);
    }

    private function add() {

        if (!$this->request->isAjax()) {
            return cmsCore::error404();
        }

        $name = $this->request->get('name', '');
        if (!$name) {
            return cmsCore::error404();
        }

        $user = $this->model_users->filterEqual('email', $name)->getUser();

        if (!$user) {

            return $this->cms_template->renderJSON([
                'error'   => true,
                'message' => sprintf(LANG_CP_USER_NOT_FOUND, $name)
            ]);
        }

        $moderators = $this->model_moderation->getContentTypeModerators($this->ctype['name']);

        if (isset($moderators[$user['id']])) {

            return $this->cms_template->renderJSON([
                'error'   => true,
                'message' => sprintf(LANG_MODERATOR_ALREADY, $user['nickname'])
            ]);
        }

        $moderator = $this->model_moderation->addContentTypeModerator($this->ctype['name'], $user['id']);

        if (!$moderator) {

            return $this->cms_template->renderJSON([
                'error'   => true,
                'message' => LANG_ERROR
            ]);
        }

        return $this->cms_template->renderJSON([
            'error' => false,
            'name'  => $user['nickname'],
            'html'  => $this->cms_template->render('ctypes_moderator', [
                'moderator' => $moderator,
                'ctype'     => $this->ctype
            ], new cmsRequest([], cmsRequest::CTX_INTERNAL)),
            'id'    => $user['id']
        ]);
    }

    private function delete() {

        if (!$this->request->isAjax()) {
            return cmsCore::error404();
        }

        $id = $this->request->get('id', 0);
        if (!$id) {
            return cmsCore::error404();
        }

        $moderators = $this->model_moderation->getContentTypeModerators($this->ctype['name']);

        if (!isset($moderators[$id])) {

            return $this->cms_template->renderJSON([
                'error' => true
            ]);
        }

        $this->model_moderation->deleteContentTypeModerator($this->ctype['name'], $id);

        return $this->cms_template->renderJSON([
            'error' => false
        ]);
    }

}
