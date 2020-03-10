<?php

class onGroupsAdminDashboardChart extends cmsAction {

	public function run(){

        $data = array(
            'id' => 'groups',
            'title' => LANG_GROUPS,
            'sections' => array(
                'grps' => array(
                    'title' => LANG_GROUPS,
                    'table' => 'groups',
                    'key' => 'date_pub'
                ),
            )
        );

        return $data;

    }

}
