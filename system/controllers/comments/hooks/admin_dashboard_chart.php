<?php

class onCommentsAdminDashboardChart extends cmsAction {

    public function run() {

        return [
            'id'       => 'comments',
            'title'    => LANG_COMMENTS,
            'sections' => [
                'comments' => [
                    'title' => LANG_COMMENTS,
                    'table' => 'comments',
                    'key'   => 'date_pub'
                ]
            ]
        ];
    }

}
