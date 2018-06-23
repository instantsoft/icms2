<?php

class backendSubscriptions extends cmsBackend {

    protected $useOptions = true;

    public $useDefaultOptionsAction = true;

    public $queue = array(
        'queues'           => array('subscriptions'),
        'queue_name'       => LANG_SBSCR_QUEUE_NAME,
        'use_queue_action' => true
    );

    public function __construct( cmsRequest $request){

        parent::__construct($request);

        array_unshift($this->backend_menu, array(
                'title' => LANG_OPTIONS,
                'url'   => href_to($this->root_url, 'options')
            ), array(
                'title' => LANG_SBSCR_LIST,
                'url'   => href_to($this->root_url, 'list')
            )
        );

    }

    public function actionIndex(){
        $this->redirectToAction('options');
    }

}
