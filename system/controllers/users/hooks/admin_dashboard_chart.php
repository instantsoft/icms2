<?php

class onUsersAdminDashboardChart extends cmsAction {

	public function run(){

        $data = array(
            'id' => 'users',
            'title' => LANG_USERS,
            'sections' => array(
                'reg' => array(
                    'title' => LANG_REGISTRATION,
                    'table' => '{users}',
                    'key' => 'date_reg'
                ),
                'log' => array(
                    'title' => LANG_AUTH_LOGIN,
                    'table' => '{users}',
                    'key' => 'date_log'
                )
            )
        );

        return $data;

    }

}
