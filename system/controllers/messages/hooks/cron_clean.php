<?php

class onMessagesCronClean extends cmsAction {

    public $disallow_event_db_register = true;

    public function run() {

        if (empty($this->options['time_delete_old'])) {
            return false;
        }

        $this->model->filterDateOlder('date_pub', $this->options['time_delete_old'])->
                filterNotNull('is_deleted')->deleteFiltered('{users}_messages');

        return true;
    }

}
