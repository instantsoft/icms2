<?php
class auth extends cmsFrontend {

    protected $useOptions = true;

//============================================================================//
//============================================================================//

	public function actionIndex(){

        $this->runAction('login');

  	}

//============================================================================//
//============================================================================//

    public function actionLogout(){

        cmsEventsManager::hook('auth_logout', cmsUser::getInstance()->id);

        cmsUser::logout();

        $this->redirectToHome();
        $this->halt();

    }

//============================================================================//
//============================================================================//

    public function isEmailAllowed($value){

        $list = $this->options['restricted_emails'];

        return !string_in_mask_list($value, $list);

    }

    public function isNameAllowed($value){

        $list = $this->options['restricted_names'];

        return !string_in_mask_list($value, $list);

    }

    public function isIPAllowed($value){

        $list = $this->options['restricted_ips'];

        return !string_in_mask_list($value, $list);

    }

//============================================================================//
//============================================================================//

}
