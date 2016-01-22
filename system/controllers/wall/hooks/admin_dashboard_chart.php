<?php

class onWallAdminDashboardChart extends cmsAction {

	public function run(){

        $data = array(
            'id' => 'wall',
            'title' => LANG_WALL,
            'sections' => array(
                'msg' => array(
                    'title' => LANG_WALL_ENTRIES,
                    'table' => 'wall_entries',
                    'key' => 'date_pub'
                ),
            )
        );

        return $data;

    }

}
