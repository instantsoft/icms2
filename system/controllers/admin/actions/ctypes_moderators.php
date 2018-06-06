<?php

class actionAdminCtypesModerators extends cmsAction {

    public function run($ctype_id, $action = 'view'){

        if (!$ctype_id) { cmsCore::error404(); }

        $this->ctype = $this->model_content->getContentType($ctype_id);
        if (!$this->ctype) { cmsCore::error404(); }

        switch ($action){

            case 'view': $this->view(); break;
            case 'add': $this->add(); break;
            case 'delete': $this->delete(); break;

            default : cmsCore::error404();

        }

        return;

    }

    private function view(){

        $moderators = $this->model_moderation->getContentTypeModerators($this->ctype['name']);

        return $this->cms_template->render('ctypes_moderators', array(
            'ctype'      => $this->ctype,
            'moderators' => $moderators
        ));

    }

    private function add(){

        if (!$this->request->isAjax()) { cmsCore::error404(); }

        $name = $this->request->get('name', '');
        if (!$name) { cmsCore::error404(); }

        $user = cmsCore::getModel('users')->filterEqual('email', $name)->getUser();

        if ($user === false){
            return $this->cms_template->renderJSON(array(
                'error'   => true,
                'message' => sprintf(LANG_CP_USER_NOT_FOUND, $name)
            ));
        }

        $moderators = $this->model_moderation->getContentTypeModerators($this->ctype['name']);

        if (isset($moderators[$user['id']])){
            return $this->cms_template->renderJSON(array(
                'error'   => true,
                'message' => sprintf(LANG_MODERATOR_ALREADY, $user['nickname'])
            ));
        }

        $moderator = $this->model_moderation->addContentTypeModerator($this->ctype['name'], $user['id']);

        if (!$moderator){
            return $this->cms_template->renderJSON(array(
                'error'   => true,
                'message' => LANG_ERROR
            ));
        }

        return $this->cms_template->renderJSON(array(
            'error' => false,
            'name'  => $user['nickname'],
            'html'  => $this->cms_template->render('ctypes_moderator', array(
                'moderator' => $moderator,
                'ctype'     => $this->ctype
            ), new cmsRequest(array(), cmsRequest::CTX_INTERNAL)),
            'id'    => $user['id']
        ));

    }

    private function delete(){

        if (!$this->request->isAjax()) { cmsCore::error404(); }

        $id = $this->request->get('id', 0);
        if (!$id) { cmsCore::error404(); }

        $moderators = $this->model_moderation->getContentTypeModerators($this->ctype['name']);

        if (!isset($moderators[$id])){
            return $this->cms_template->renderJSON(array(
                'error' => true
            ));
        }

        $this->model_moderation->deleteContentTypeModerator($this->ctype['name'], $id);

        return $this->cms_template->renderJSON(array(
            'error' => false
        ));

    }

}
