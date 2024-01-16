<?php

class onGroupsAdminDashboardChart extends cmsAction {

    public function run() {

        return [
            'id'       => 'groups',
            'title'    => LANG_GROUPS,
            'sections' => [
                'grps' => [
                    'title' => LANG_GROUPS,
                    'table' => 'groups',
                    'key'   => 'date_pub'
                ]
            ]
        ];
    }

}
