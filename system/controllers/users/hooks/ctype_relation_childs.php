<?php

class onUsersCtypeRelationChilds extends cmsAction {

    public function run($ctype_id){

        return array(
            'name'  => $this->name,
            'types' => array(
                'users:' => LANG_USERS_CONTROLLER
            )
        );

    }

}
