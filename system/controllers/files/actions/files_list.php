<?php

class actionFilesFilesList extends cmsAction {

    public $request_params = array(
        'target_controller' => array(
            'default' => '',
            'rules'   => array(
                array('sysname'),
                array('max_length', 32)
            )
        ),
        'target_subject' => array(
            'default' => '',
            'rules'   => array(
                array('regexp', "/^([a-z0-9\-_\/\.]*)$/"),
                array('max_length', 32)
            )
        ),
        'target_id' => array(
            'default' => 0,
            'rules'   => array(
                array('digits')
            )
        )
    );

    public function run($type){

        if (!$this->cms_user->is_logged) {
            return $this->cms_template->renderJSON(array());
        }

        $target_controller = $this->request->get('target_controller');
        $target_subject    = $this->request->get('target_subject');
        $target_id         = $this->request->get('target_id');

        if(!$target_controller){
            return $this->cms_template->renderJSON(array());
        }
        $this->model->filterEqual('target_controller', $target_controller);

        if(!$target_subject){
            return $this->cms_template->renderJSON(array());
        }
        $this->model->filterEqual('target_subject', $target_subject);

        if($target_id){
            $this->model->filterEqual('target_id', $target_id);
        }

        $this->model->filterEqual('user_id', $this->cms_user->id);

        $this->model->limit(100);

        $files = $this->model->filterFileType($type)->getFiles();

        if(!$files){
            return $this->cms_template->renderJSON(array());
        }

        return $this->cms_template->renderJSON($files);

    }

}
