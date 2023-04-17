<?php

class backendComments extends cmsBackend {

    public $useDefaultOptionsAction     = true;
    public $useDefaultPermissionsAction = true;
    public $useSeoOptions = true;

    protected $useDefaultModerationAction = true;
    protected $useOptions = true;

    public function __construct(cmsRequest $request) {

        parent::__construct($request);

        array_unshift($this->backend_menu,
            [
                'title' => LANG_COMMENTS_LIST,
                'url'   => href_to($this->root_url),
                'options' => [
                    'icon' => 'comments'
                ]
            ],
            [
                'title' => LANG_OPTIONS,
                'url'   => href_to($this->root_url, 'options'),
                'options' => [
                    'icon' => 'cog'
                ]
            ],
            [
                'title' => LANG_PERMISSIONS,
                'url'   => href_to($this->root_url, 'perms', 'comments'),
                'options' => [
                    'icon' => 'key'
                ]
            ]
        );
    }

}
