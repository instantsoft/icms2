<?php

class actionAdminCtypesModerators extends cmsAction {

    public function run($ctype_id, $action='view'){

        if (!$ctype_id) { cmsCore::error404(); }

        $this->content_model = cmsCore::getModel('content');
        $this->ctype = $this->content_model->getContentType($ctype_id);

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

        $moderators = $this->content_model->getContentTypeModerators($this->ctype['name']);

        return cmsTemplate::getInstance()->render('ctypes_moderators', array(
            'ctype' => $this->ctype,
            'moderators' => $moderators
        ));

    }

    private function add(){

        if (!$this->request->isAjax()) { cmsCore::error404(); }

        $name = $this->request->get('name');

        if (!$name) { cmsCore::error404(); }

        $template = cmsTemplate::getInstance();

        $users_model = cmsCore::getModel('users');

        $user = $users_model->filterEqual('nickname', $name)->getUser();

        if ($user===false){
            return $template->renderJSON(array(
                'error' => true,
                'message' => sprintf(LANG_CP_USER_NOT_FOUND, $name)
            ));
        }

        $moderators = $this->content_model->getContentTypeModerators($this->ctype['name']);

        if (isset($moderators[$user['id']])){
            return $template->renderJSON(array(
                'error' => true,
                'message' => sprintf(LANG_MODERATOR_ALREADY, $name)
            ));
        }

        $moderator = $this->content_model->addContentTypeModerator($this->ctype['name'], $user['id']);

        if (!$moderator){
            return $template->renderJSON(array(
                'error' => true,
                'message' => LANG_ERROR
            ));
        }
        
        return $template->renderJSON(array(
            'error' => false,
            'name' => $name,
            'html' => $template->render('ctypes_moderator', array(
                'moderator' => $moderator,
                'ctype' => $this->ctype
            ), new cmsRequest(array(), cmsRequest::CTX_INTERNAL)),
            'id' => $user['id'],
        ));

    }

    private function delete(){

        if (!$this->request->isAjax()) { cmsCore::error404(); }

        $id = $this->request->get('id');

        if (!$id) { cmsCore::error404(); }

        $template = cmsTemplate::getInstance();

        $moderators = $this->content_model->getContentTypeModerators($this->ctype['name']);

        if (!isset($moderators[$id])){
            return $template->renderJSON(array(
                'error' => true,
            ));
        }

        $this->content_model->deleteContentTypeModerator($this->ctype['name'], $id);

        return $template->renderJSON(array(
            'error' => false,
        ));

    }

}
