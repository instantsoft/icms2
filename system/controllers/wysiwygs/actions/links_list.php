<?php

class actionWysiwygsLinksList extends cmsAction {

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
                array('sysname'),
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

    public function run(){

        if (!$this->cms_user->is_logged) {
            return $this->cms_template->renderJSON(array());
        }

        $target_controller = $this->request->get('target_controller');
        $target_subject    = $this->request->get('target_subject');
        $target_id         = $this->request->get('target_id');

        if(!cmsCore::isControllerExists($target_controller)){
            return $this->cms_template->renderJSON(array());
        }

        $controller = cmsCore::getController($target_controller, $this->request);

        if(!$controller->isEnabled()){
            return $this->cms_template->renderJSON(array());
        }

        $data = $controller->runHook('wysiwyg_links_list', array($target_subject, $target_id));

        if(!$data || $data === $this->request->getData()){
            return $this->cms_template->renderJSON(array());
        }

        return $this->cms_template->renderJSON($data);

    }

}
