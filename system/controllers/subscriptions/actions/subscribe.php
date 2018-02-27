<?php

class actionSubscriptionsSubscribe extends cmsAction {

    private $target = array();
    private $subscribe = array();

    public $request_params = array(
        'tc' => array(
            'default' => '',
            'rules'   => array(
                array('required'),
                array('sysname')
            )
        ),
        'ts' => array(
            'default' => '',
            'rules'   => array(
                array('sysname')
            )
        ),
        'guest_email' => array(
            'default' => '',
            'rules'   => array(
                array('email'),
                array('max_length', 100)
            )
        ),
        'guest_name' => array(
            'default' => '',
            'rules'   => array(
                array('regexp', '/^([0-9a-zа-яёй\.\@\,\ \-]+)$/ui'),
                array('max_length', 50)
            )
        )
    );

    public function run(){

        if (!$this->request->isAjax()) { cmsCore::error404(); }

        $this->target['controller'] = $this->request->get('tc', '');
        $this->target['subject']    = $this->request->get('ts', '');
        $this->target['params']     = $this->request->get('target_params', array());

        if(!cmsCore::isControllerExists($this->target['controller']) ||
                !cmsController::enabled($this->target['controller'])){

            return $this->cms_template->renderJSON(array(
                'error' => true
            ));

        }

        if(!$this->target['subject']){

            $controller = cmsCore::getController($this->target['controller']);

            $subscribe_subjects_list = $controller->runHook('subscribe_subjects_list');

            // если есть список субъектов, даём выбор
            if($subscribe_subjects_list){

                return $this->cms_template->renderJSON(array(
                    'error'   => false,
                    'confirm' => ''
                ));

            } else {
                $this->target['subject'] = null;
            }

        }

        if($this->cms_user->is_logged){

            $this->subscribe['user_id'] = $this->cms_user->id;

        } else {

            $this->subscribe['guest_email'] = $this->request->get('guest_email', '');
            $this->subscribe['guest_name']  = $this->request->get('guest_name', '');

            if(!$this->subscribe['guest_email']){
                return $this->cms_template->renderJSON(array('error' => true, 'errors' => array(
                    'guest_email' => ERR_VALIDATE_REQUIRED
                )));
            }

            if(!$this->subscribe['guest_name']){
                return $this->cms_template->renderJSON(array('error' => true, 'errors' => array(
                    'guest_name' => ERR_VALIDATE_REQUIRED
                )));
            }

        }

        $this->model->subscribe($this->target, $this->subscribe);

        cmsEventsManager::hook('subscribe', array($this->target, $this->subscribe));

        return $this->cms_template->renderJSON(array(
            'error' => false,
            'message'=>''
        ));

    }

}
