<?php

class onBillingUserTabInfo extends cmsAction {

    public function run($profile, $tab_name) {

        if ($this->cms_user->id != $profile['id'] && !$this->cms_user->is_admin) {
            return false;
        }

        return ['counter' => (float) $profile['balance']];
    }

}
