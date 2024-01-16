<?php

class onGroupsAdminGroupsDatasetFieldsList extends cmsAction {

    public function run($fields_list) {

        $fields_list[] = [
            'value' => 'date_pub',
            'type'  => 'date',
            'title' => LANG_DATE_PUB
        ];

        $fields_list[] = [
            'value' => 'rating',
            'type'  => 'int',
            'title' => LANG_RATING
        ];

        $fields_list[] = [
            'value' => 'members_count',
            'type'  => 'int',
            'title' => LANG_GROUPS_GROUP_MEMBERS
        ];

        return $fields_list;
    }

}
