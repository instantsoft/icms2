<?php

class onWallAdminDashboardChart extends cmsAction {

    public function run() {

        return [
            'id' => 'wall',
            'title'    => LANG_WALL,
            'sections' => [
                'msg' => [
                    'title' => LANG_WALL_ENTRIES,
                    'table' => 'wall_entries',
                    'key'   => 'date_pub'
                ]
            ]
        ];
    }

}
