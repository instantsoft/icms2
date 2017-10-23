<?php

class onContentCtypeListsContext extends cmsAction {

    public function run(){

        return array(
            'category_view'           => LANG_CONTENT_CONTEXT_LT_CATEGORY_VIEW,
            'item_view_relation_tab'  => LANG_CONTENT_CONTEXT_LT_ITEM_VIEW_RELATION_TAB,
            'item_view_relation_list' => LANG_CONTENT_CONTEXT_LT_ITEM_VIEW_RELATION_LIST,
            'items_from_friends'      => LANG_CONTENT_CONTEXT_LT_ITEMS_FROM_FRIENDS,
            'trash'                   => LANG_CONTENT_CONTEXT_LT_TRASH,
            'moderation_list'         => LANG_CONTENT_CONTEXT_LT_MODERATION_LIST,
            'profile_content'         => LANG_CONTENT_CONTEXT_LT_PROFILE_CONTENT
        );

    }

}
