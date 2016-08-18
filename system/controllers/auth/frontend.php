<?php
class auth extends cmsFrontend {

    protected $useOptions = true;
    public $useSeoOptions = true;

//============================================================================//
//============================================================================//

	public function actionIndex(){

        $this->runAction('login');

  	}

//============================================================================//
//============================================================================//

    public function actionLogout(){

        cmsEventsManager::hook('auth_logout', $this->cms_user->id);

        cmsUser::logout();

        if(!function_exists('get_headers')){
            $this->redirectToHome();
        }

        $back_url = $this->getBackURL();

        $h = get_headers($this->getBackURL(), true);
        $code = substr($h[0], 9, 3);

        if((int)$code < 400){
            $this->redirect($back_url);
        }

        $this->redirectToHome();

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

    public function getAuthRedirectUrl($value){

        $url = href_to_home();

		$user_id = cmsUser::sessionGet('user:id');
		if (!$user_id){ return $url; }

        $back_url = $this->getBackURL();
        if(strpos($back_url, href_to('auth', 'login')) !== false) {
            $back_url = $url;
        }
		switch($value){
			case 'none':        $url = $back_url; break;
			case 'index':       $url = href_to_home(); break;
			case 'profile':     $url = href_to('users', $user_id); break;
			case 'profileedit': $url = href_to('users', $user_id, 'edit'); break;
		}

		return $url;

    }

}
