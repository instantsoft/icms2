<?php

class onContentAdminDashboardChart extends cmsAction {

	public function run(){

		$ctypes = $this->model->getContentTypes();

        $data = array(
            'id' => 'content',
            'title' => LANG_CONTENT,
            'sections' => array()
        );

		foreach($ctypes as $ctype){
            $data['sections'][$ctype['name']] = array(
                'title' => $ctype['title'],
                'table' => $this->model->getContentTypeTableName($ctype['name']),
                'key' => 'date_pub'
            );
		}

        return $data;

    }

}
