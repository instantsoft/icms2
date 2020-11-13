<?php

class actionFilesUploadWithWysiwyg extends cmsAction {

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
        ),
        'filetype' => array(
            'default' => '',
            'rules'   => array(
                array('required'),
                array('sysname'),
                array('max_length', 32)
            )
        )
    );

    public function run($name){

        if (!$this->cms_user->is_logged) {
            return $this->cms_template->renderJSON(array('error' => false));
        }

        if (!cmsForm::validateCSRFToken($this->request->get('csrf_token', ''))){
            cmsCore::error404();
        }

        $target_controller = $this->request->get('target_controller');
        $target_subject    = $this->request->get('target_subject');
        $target_id         = $this->request->get('target_id');

        $perm_key_params = [];

        if($target_controller){
            $perm_key_params['target_controller'] = $target_controller;
        }

        if($target_subject){
            $perm_key_params['target_subject'] = $target_subject;
        }

        if($target_id){
            $perm_key_params['target_id'] = $target_id;
        }

        $allowed_mime_types = cmsUser::sessionGet('ww_allowed_mime_types'.($perm_key_params ? ':'.implode(':', $perm_key_params) : ''));

        if (!$allowed_mime_types){
            cmsCore::error404();
        }

        $this->cms_uploader->setAllowedMime($allowed_mime_types);

        $result = $this->cms_uploader->upload($name);

        if (!$result['success']){
            if(!empty($result['path'])){
                files_delete_file($result['path'], 2);
            }
            return $this->cms_template->renderJSON($result);
        }

        unset($result['path']);

        $this->model->registerFile(array_merge(array(
            'path'    => $result['url'],
            'type'    => $this->request->get('filetype'),
            'name'    => $result['name'],
            'user_id' => $this->cms_user->id
        ), $perm_key_params));

        return $this->cms_template->renderJSON([
            'success'  => true,
            'name'     => $result['name'],
            'location' => $this->cms_config->upload_host . '/' .$result['url']
        ]);

    }

}
