<?php

class onUsersMenuUsers extends cmsAction {

    public function run($item){

        $action = $item['action'];

        $user = cmsUser::getInstance();

        if ($action == 'profile'){

            return array(
                'url' => href_to($this->name, $user->id),
                'items' => false
            );

        }

        if ($action == 'settings'){

            return array(
                'url' => href_to($this->name, 'edit', array($user->id)),
                'items' => false
            );

        }

    }

}
