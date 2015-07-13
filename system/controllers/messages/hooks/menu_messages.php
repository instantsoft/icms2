<?php

class onMessagesMenuMessages extends cmsAction {

    public function run($item){

        $user = cmsUser::getInstance();

        if (!$user->is_logged) { return false; }

        $action = $item['action'];

        if ($action == 'view'){

            $count = $this->model->getNewMessagesCount($user->id);

            return array(
                'url' => href_to($this->name),
                'counter' => $count
            );

        }

        if ($action == 'notices'){

            $count = $this->model->getNoticesCount($user->id);

            return $count ? array(
                'url' => href_to($this->name, 'notices'),
                'counter' => $count
            ) : false;

        }

    }

}
