<?php

class onGroupsAdminGroupsDatasetFieldsList extends cmsAction {

	public function run($fields_list){

        $fields_list[] = array(
            'value' => 'date_pub',
            'type'  => 'date',
            'title' => LANG_DATE_PUB
        );

        $fields_list[] = array(
            'value' => 'rating',
            'type'  => 'int',
            'title' => LANG_RATING
        );

        $fields_list[] = array(
            'value' => 'members_count',
            'type'  => 'int',
            'title' => LANG_GROUPS_GROUP_MEMBERS
        );

        return $fields_list;

    }

}
