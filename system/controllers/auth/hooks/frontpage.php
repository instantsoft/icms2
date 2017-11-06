<?php

class onAuthFrontpage extends cmsAction {

    public function run($action){

        if ($this->cms_user->is_logged){ $this->redirectTo('users', $this->cms_user->id); }

        return $this->runAction($action);

    }

}
