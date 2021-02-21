<?php

class onModerationCronTrash extends cmsAction {

    public $disallow_event_db_register = true;

    public function run() {

        $this->model->deleteExpiredTrashContentItems();

        return true;
    }

}
