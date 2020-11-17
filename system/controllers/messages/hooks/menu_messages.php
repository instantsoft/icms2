<?php

class onMessagesMenuMessages extends cmsAction {

    public function run($item) {

        if (!$this->cms_user->is_logged) {
            return false;
        }

        $action = $item['action'];

        if ($action == 'view' && !empty($this->options['is_enable_pm'])) {

            $count = $this->model->getNewMessagesCount($this->cms_user->id);

            return [
                'url'     => href_to($this->name),
                'counter' => $count
            ];
        }

        if ($action == 'notices') {

            $count = $this->model->getNoticesCount($this->cms_user->id);

            return $count ? [
                'url'     => href_to($this->name, 'notices'),
                'counter' => $count
            ] : false;
        }

        return false;
    }

}
