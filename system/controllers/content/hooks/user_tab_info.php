<?php

class onContentUserTabInfo extends cmsAction {

    public function run($profile, $tab_name){

        if (!isset($this->content_counts)){
            $this->content_counts = $this->model->getUserContentCounts($profile['id']);
        }

        if (!isset($this->content_counts[$tab_name])){ return false; }

        return array(
            'counter' => $this->content_counts[$tab_name]['count'],
            'url' => href_to('users', $profile['id'], $tab_name),
        );

    }

}
