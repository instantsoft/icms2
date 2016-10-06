<?php

class onGroupsUserTabInfo extends cmsAction {

    public function run($profile, $tab_name){

        if (!$this->cms_user->isPrivacyAllowed($profile, 'view_user_groups')){
            return false;
        }

        $this->count = $this->model->filterByMember($profile['id'])->getGroupsCount();

        $this->model->resetFilters();

        if (!$this->count){ return false; }

        return array('counter'=>$this->count);

    }

}
