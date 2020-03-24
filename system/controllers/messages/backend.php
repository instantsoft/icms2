<?php

class backendMessages extends cmsBackend {

    public $useDefaultOptionsAction = true;

    public $queue = array(
        'queues'           => array('email'),
        'queue_name'       => LANG_EMAIL,
        'use_queue_action' => true
    );

    public function __construct( cmsRequest $request){

        parent::__construct($request);

        array_unshift($this->backend_menu, array(
                'title' => LANG_OPTIONS,
                'url'   => href_to($this->root_url, 'options')
            ), array(
                'title' => LANG_PM_PMAILING,
                'url'   => href_to($this->root_url, 'pmailing')
            )
        );

    }

    public function actionIndex(){
        $this->redirectToAction('options');
    }

}
