<?php

class onModerationMenuModeration extends cmsAction {

    public function run($item){

        if (!$this->cms_user->is_logged) { return false; }

        $action = $item['action'];

        if ($action == 'panel'){

            $counts = $this->model->getTasksCounts($this->cms_user->id, $this->cms_user->is_admin);
            if (!$counts) { return false; }

            $total = array_sum($counts);

            return array(
                'url'     => href_to($this->name),
                'counter' => $total
            );

        }

        if ($action == 'user_panel'){

            $counts = $this->model->getUserTasksCounts($this->cms_user->id);
            if (!$counts) { return false; }

            $total = array_sum($counts);

            return array(
                'url'     => href_to($this->name, 'waiting_list'),
                'counter' => $total
            );

        }

        if($action == 'draft') {

            $counts = $this->getUserDraftCounts($this->cms_user->id);
            if (!$counts) { return false; }

            $total = array_sum($counts);

            return array(
                'url' => href_to($this->name, 'draft'),
                'counter' => $total
            );

        }

        return false;

    }

}
