<?php

class backendCommentsvk extends cmsBackend {

    public $useDefaultOptionsAction = true;
    protected $useOptions = true;

    public function actionIndex() {
        $this->redirectToAction('options');
    }

    public function getBackendMenu() {
        return array(
            array(
                'title' => LANG_OPTIONS,
                'url'   => href_to($this->root_url, 'options')
            ),
            array(
                'title' => LANG_COM_VK_ADMIN_LIST,
                'url'   => href_to($this->root_url, 'comments_list')
            ),
        );
    }

}
