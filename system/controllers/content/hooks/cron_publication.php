<?php

class onContentCronPublication extends cmsAction {

	public function run(){

		$ctypes = $this->model->getContentTypes();

		foreach($ctypes as $ctype){

			if (!$ctype['is_date_range']) { continue; }

			$this->model->publishDelayedContentItems($ctype['name']);

            if(isset($ctype['options']['is_date_range_process']) && $ctype['options']['is_date_range_process'] === 'delete'){ // удалить isset в след релизе
                $this->model->deleteExpiredContentItems($ctype['name']);
            }else{
                $this->model->hideExpiredContentItems($ctype['name']);
            }

		}

    }

}
