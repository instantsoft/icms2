<?php

class onContentAdminContentDatasetFieldsList extends cmsAction {

    public function run($fields_list) {

        $fields_list[] = [
            'value' => 'rating',
            'type'  => 'int',
            'title' => LANG_RATING
        ];

        $fields_list[] = [
            'value' => 'comments',
            'type'  => 'int',
            'title' => LANG_COMMENTS
        ];

        $fields_list[] = [
            'value' => 'hits_count',
            'type'  => 'int',
            'title' => LANG_HITS
        ];

        $fields_list[] = [
            'value' => 'user_id',
            'type'  => 'int',
            'title' => LANG_AUTHOR
        ];

        return $fields_list;
    }

}
