<?php

class onContentCtypeListsContext extends cmsAction {

    private $lists = array(
        'template' => array(
            'category_view'           => LANG_CONTENT_CONTEXT_LT_CATEGORY_VIEW,
            'item_view_relation_tab'  => LANG_CONTENT_CONTEXT_LT_ITEM_VIEW_RELATION_TAB,
            'item_view_relation_list' => LANG_CONTENT_CONTEXT_LT_ITEM_VIEW_RELATION_LIST,
            'form_field'              => LANG_CONTENT_CONTEXT_LT_FORM_FIELD,
            'items_from_friends'      => LANG_CONTENT_CONTEXT_LT_ITEMS_FROM_FRIENDS,
            'trash'                   => LANG_CONTENT_CONTEXT_LT_TRASH,
            'moderation_list'         => LANG_CONTENT_CONTEXT_LT_MODERATION_LIST,
            'profile_content'         => LANG_CONTENT_CONTEXT_LT_PROFILE_CONTENT
        ),
        'dataset' => array(
            'category_view'          => LANG_CONTENT_CONTEXT_LT_CATEGORY_VIEW,
            'item_view_relation_tab' => LANG_CONTENT_CONTEXT_LT_ITEM_VIEW_RELATION_TAB,
            'profile_content'        => LANG_CONTENT_CONTEXT_LT_PROFILE_CONTENT
        )
    );

    public function run($context = null){

        if($context === null){
            return $this->lists;
        }

        $contexts = explode(':', $context);

        $context = $contexts[0];
        $context_subj = isset($contexts[1]) ? $contexts[1] : false;

        if(!empty($this->lists[$context])){
            return $this->lists[$context];
        }

        return false;

    }

}
