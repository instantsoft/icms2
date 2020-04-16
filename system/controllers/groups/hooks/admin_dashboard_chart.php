<?php

class onGroupsAdminDashboardChart extends cmsAction {

	public function run(){

        $data = array(
<<<<<<< HEAD
            'id' => 'users',
=======
            'id' => 'groups',
>>>>>>> origin/master
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
