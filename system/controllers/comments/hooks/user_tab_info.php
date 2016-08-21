<?php

class onCommentsUserTabInfo extends cmsAction {

    public function run($profile, $tab_name){

        if(!empty($this->options['disable_icms_comments'])){
            return false;
        }

        $this->count = $this->model->
                                filterEqual('user_id', $profile['id'])->
                                filterIsNull('is_deleted')->
                                getCommentsCount();

        if (!$this->count){ return false; }

        return array('counter'=>$this->count);

    }

}
