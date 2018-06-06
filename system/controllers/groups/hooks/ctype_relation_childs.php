<?php

class onGroupsCtypeRelationChilds extends cmsAction {

    public function run($ctype_id){

        return array(
            'name'  => $this->name,
            'types' => array(
                'groups:' => LANG_GROUPS_CONTROLLER
            )
        );

    }

}
