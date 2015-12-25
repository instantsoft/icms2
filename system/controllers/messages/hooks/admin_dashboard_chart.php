<?php

class onMessagesAdminDashboardChart extends cmsAction {

	public function run(){

        $data = array(
            'id' => 'messages',
            'title' => LANG_MESSAGES_CONTROLLER,
            'sections' => array(
                'msg' => array(
                    'title' => LANG_MESSAGES_CONTROLLER,
                    'table' => 'users_messages',
                    'key' => 'date_pub'
                ),
            )
        );

        return $data;

    }

}
