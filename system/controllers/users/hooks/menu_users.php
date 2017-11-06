<?php

class onUsersMenuUsers extends cmsAction {

    public function run($item){

        $action = $item['action'];

        if ($action == 'profile'){

            return array(
                'url'   => href_to($this->name, $this->cms_user->id),
                'items' => false
            );

        }

        if ($action == 'settings'){

            return array(
                'url'   => href_to($this->name, 'edit', array($this->cms_user->id)),
                'items' => false
            );

        }

    }

}
