<?php

class onCommentsAdminDashboardChart extends cmsAction {

	public function run(){

        $data = array(
            'id' => 'users',
            'title' => LANG_COMMENTS,
            'sections' => array(
                'comments' => array(
                    'title' => LANG_COMMENTS,
                    'table' => 'comments',
                    'key' => 'date_pub'
                ),
            )
        );

        return $data;

    }

}
