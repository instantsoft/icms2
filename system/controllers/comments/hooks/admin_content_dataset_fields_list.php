<?php

class onCommentsAdminContentDatasetFieldsList extends cmsAction {

    public function run($fields_list) {

        $fields_list[] = [
            'value' => 'comments',
            'type'  => 'int',
            'title' => LANG_COMMENTS
        ];

        return $fields_list;
    }

}
