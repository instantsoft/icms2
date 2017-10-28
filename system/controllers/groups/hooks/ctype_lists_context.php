<?php

class onGroupsCtypeListsContext extends cmsAction {

    private $lists = array(
        'template' => array(
            'group_content' => LANG_GROUPS_CONTEXT_LT_GROUP_CONTENT
        ),
        'dataset' => array(
            'group_content' => LANG_GROUPS_CONTEXT_LT_GROUP_CONTENT
        ),
    );

    public function run($context = null){

        if($context === null){
            return $this->lists;
        }

        $contexts = explode(':', $context);

        $context = $contexts[0];
        $context_subj = isset($contexts[1]) ? $contexts[1] : false;

        if($context_subj == $this->name){
            return false;
        }

        if(!empty($this->lists[$context])){
            return $this->lists[$context];
        }

        return false;

    }

}
