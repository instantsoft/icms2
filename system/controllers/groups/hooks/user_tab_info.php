<?php

class onGroupsUserTabInfo extends cmsAction {

    public function run($profile, $tab_name){

        if (!$this->cms_user->isPrivacyAllowed($profile, 'view_user_groups')){
            return false;
        }

        if ($this->cms_user->id == $profile['id'] || $this->cms_user->is_admin){
            $this->model->disableApprovedFilter();
        }

        $this->count = $this->model->filterByMember($profile['id'])->getGroupsCount();

        $this->model->resetFilters();

        if (!$this->count){ return false; }

        return array('counter'=>$this->count);

    }

}
