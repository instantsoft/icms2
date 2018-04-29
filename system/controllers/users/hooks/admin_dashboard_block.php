<?php

class onUsersAdminDashboardBlock extends cmsAction {

	public function run(){

        $profiles = cmsCore::getModel('users')->filterOnlineUsers()->getUsers();
        if (!$profiles) { return false; }

        $dashboard_blocks = array();

        $dashboard_blocks[] = array(
            'title' => LANG_CP_USERS_ONLINE.' '.html_spellcount(count($profiles), LANG_USERS_SPELL),
            'html'  => $this->cms_template->renderInternal($this, 'backend/admin_dashboard_online_block', array(
                'profiles' => $profiles
            ))
        );

        return $dashboard_blocks;

    }

}
