<?php

class onMessagesAdminDashboardChart extends cmsAction {

    public function run() {

        if (empty($this->options['is_enable_pm'])) {
            return false;
        }

        $data = [
            'id'       => 'messages',
            'title'    => LANG_MESSAGES_CONTROLLER,
            'sections' => [
                'msg' => [
                    'title' => LANG_MESSAGES_CONTROLLER,
                    'table' => 'users_messages',
                    'key'   => 'date_pub'
                ]
            ]
        ];

        return $data;
    }

}
