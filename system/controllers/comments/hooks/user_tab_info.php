<?php

class onCommentsUserTabInfo extends cmsAction {

    public function run($profile, $tab_name){

        if(!empty($this->options['disable_icms_comments'])){
            return false;
        }

        if (!$this->cms_user->isPrivacyAllowed($profile, 'view_user_comments')){
            return false;
        }

        if($profile['id'] == $this->cms_user->id || $this->cms_user->is_admin || cmsCore::getModel('moderation')->userIsContentModerator($this->name, $this->cms_user->id)){
            $this->model->disableApprovedFilter();
        }

        $this->count = $this->model->
                filterEqual('user_id', $profile['id'])->
                filterIsNull('is_deleted')->getCommentsCount();

        $this->model->resetFilters()->enableApprovedFilter();

        if (!$this->count){ return false; }

        return array('counter'=>$this->count);

    }

}
