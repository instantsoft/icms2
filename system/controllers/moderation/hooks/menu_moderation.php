<?php

class onModerationMenuModeration extends cmsAction {

    public function run($item){

        $user = cmsUser::getInstance();

        if (!$user->is_logged) { return false; }

        $action = $item['action'];

        if ($action == 'panel'){

            $counts = $this->model->getTasksCounts($user->id);

            if (!$counts) { return false; }

            $total = array_sum($counts);

            return array(
                'url' => href_to($this->name),
                'counter' => $total
            );

        }

        return false;

    }

}
