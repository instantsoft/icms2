<?php

class backendSubscriptions extends cmsBackend {

    use \icms\controllers\admin\traits\queueActions;

    protected $useOptions = true;
    public $useDefaultOptionsAction = true;

    /**
     * Для трейта queueActions
     * @var array
     */
    public $queue = [
        'queues'           => ['subscriptions'],
        'queue_name'       => LANG_SBSCR_QUEUE_NAME,
        'use_queue_action' => true
    ];

    public function __construct(cmsRequest $request) {

        parent::__construct($request);

        array_unshift($this->backend_menu,
            [
                'title' => LANG_OPTIONS,
                'url'   => href_to($this->root_url),
                'options' => [
                    'icon' => 'cog'
                ]
            ],
            [
                'title' => LANG_SBSCR_LIST,
                'url'   => href_to($this->root_url, 'list'),
                'options' => [
                    'icon' => 'list'
                ]
            ]
        );

    }

}
