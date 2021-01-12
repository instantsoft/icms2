<?php

class onAuthCronDeleteExpiredUnverified extends cmsAction {

    public $disallow_event_db_register = true;

    public function run() {

        $verify_exp = empty($this->options['verify_exp']) ? 48 : $this->options['verify_exp'];

        $users_model = cmsCore::getModel('users');

        $users_model->filterNotNull('is_locked')->
                filterNotNull('pass_token')->
                filterDateOlder('date_reg', $verify_exp, 'HOUR')->
                filterIsNull('ip')->
                filterIsNull('lock_until');

        $users = $users_model->getUsers();
        if (!$users) { return false; }

        foreach ($users as $user) {

            $user = cmsEventsManager::hook('user_delete', $user);

            $users_model->deleteUser($user['id']);
        }

        return true;
    }

}
