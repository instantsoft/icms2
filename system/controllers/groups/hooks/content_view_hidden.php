<?php

class onGroupsContentViewHidden extends cmsAction {

    public function run($data){

        $viewable = $data['viewable'];
        $item = $data['item'];

        if (!$viewable) { return $data; }

        if (!$item['parent_type'] == 'group') { return $data; }

        $user = cmsUser::getInstance();

        if (!$user->is_logged){ $data['viewable'] = false; return $data; }

        $membership = $this->model->getMembership($item['parent_id'], $user->id);

        if ($membership === false){ $data['viewable'] = false; return $data;; }

        return $data;

    }

}
