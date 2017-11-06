<?php

class onModerationCronTrash extends cmsAction {

	public function run(){

		$this->model->deleteExpiredTrashContentItems();

        return true;

    }

}
