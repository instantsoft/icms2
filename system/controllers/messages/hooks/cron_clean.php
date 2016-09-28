<?php

class onMessagesCronClean extends cmsAction {

	public function run(){

        if(empty($this->options['time_delete_old'])){
            return false;
        }

        $this->model->filterDateOlder('date_pub', $this->options['time_delete_old'])->deleteFiltered('{users}_messages');

        return true;

    }

}
