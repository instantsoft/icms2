<?php

class onRatingAdminContentDatasetFieldsList extends cmsAction {

    public function run($fields_list) {

        $fields_list[] = [
            'value' => 'rating',
            'type'  => 'int',
            'title' => LANG_RATING
        ];

        return $fields_list;
    }

}
