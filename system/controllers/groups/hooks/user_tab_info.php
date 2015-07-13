<?php

class onGroupsUserTabInfo extends cmsAction {

    public function run($profile, $tab_name){

        $this->count = $this->model->
                                filterByMember($profile['id'])->
                                getGroupsCount();
        
        $this->model->resetFilters();

        if (!$this->count){ return false; }

        return array('counter'=>$this->count);

    }

}
