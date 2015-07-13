<?php

class onContentCronPublication extends cmsAction {

	public function run(){

		$ctypes = $this->model->getContentTypes();
		
		foreach($ctypes as $ctype){
			
			if (!$ctype['is_date_range']) { continue; }
			
			$this->model->publishDelayedContentItems($ctype['name']);			
			
			$this->model->hideExpiredContentItems($ctype['name']);			
			
		}

    }
	
}
