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

    public function authRedirectUrl($value){

		$user_id = cmsUser::sessionGet('user:id');
		
		if (!$user_id){ return href_to_home(); }
		
		switch($value){
			case 'none':        $url = href_to_current(); break;
			case 'index':       $url = href_to_home(); break;
			case 'profile':     $url = href_to('users', $user_id); break;
			case 'profileedit': $url = href_to('users', $user_id, 'edit'); break;
		}

		return $url;

    }

}
