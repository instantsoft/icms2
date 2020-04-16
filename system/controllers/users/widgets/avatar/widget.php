<?php
class widgetUsersAvatar extends cmsWidget {

	public $is_cacheable = false;
	
    public function run(){

        $user = cmsUser::getInstance();
        
        if (!$user->is_logged) { return false; }
        
        return array(
            'user' => $user,
        );

    }

}
