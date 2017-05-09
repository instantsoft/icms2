<?php

class onGroupsContentPrivacyTypes extends cmsAction {

    public function run($data){

        list($ctype, $fields, $action, $item) = $data;

        $group_id = $this->cms_core->request->get('group_id', 0);

        if(!empty($group_id) || !empty($item['parent_id'])){

            return array(
                'name'  => $this->name,
                'types' => array(
                    3 => LANG_PRIVACY_GROUPS,
                    4 => sprintf(LANG_PRIVACY_GROUPS_ADD, $ctype['labels']['many'])
                )
            );

        }

        return false;

    }

}
