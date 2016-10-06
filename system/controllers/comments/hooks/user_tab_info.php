<?php

class onCommentsUserTabInfo extends cmsAction {

    public function run($profile, $tab_name){

        if(!empty($this->options['disable_icms_comments'])){
            return false;
        }

        if (!$this->cms_user->isPrivacyAllowed($profile, 'view_user_comments')){
            return false;
        }

        $this->count = $this->model->
                filterEqual('user_id', $profile['id'])->
                filterIsNull('is_deleted')->getCommentsCount();

        $this->model->resetFilters();

        if (!$this->count){ return false; }

        return array('counter'=>$this->count);

    }

}
