<?php

class onAuthCronDeleteLocked extends cmsAction {

    public function run(){

        if (!$this->options['verify_exp']) { return false; }
        
        $verify_exp = $this->options['verify_exp'];
       
        $users_model = cmsCore::getModel('users');

        $users_model->filterNotNull('is_locked');

        $users_model->
                    filterStart()-> 
                        filterNotNull('pass_token')->
                        filterDateOlder('date_reg', $verify_exp, 'HOUR')->
                        filterLike('lock_reason', LANG_REG_CFG_VERIFY_LOCK_REASON)->
                    filterEnd();

        $users = $users_model->getUsers();

        if (!$users) { return false; }

        foreach($users as $user){
			
            cmsCore::getModel('activity')->deleteUserEntries($user['id']);
			
            $users_model->deleteUser($user['id']);

        }

    }

}
