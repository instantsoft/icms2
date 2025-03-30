<?php

class actionWysiwygsLinksList extends cmsAction {

    public $request_params = [
        'target_controller' => [
            'default' => '',
            'rules'   => [
                ['sysname'],
                ['max_length', 32]
            ]
        ],
        'target_subject' => [
            'default' => '',
            'rules'   => [
                ['sysname'],
                ['max_length', 32]
            ]
        ],
        'target_id' => [
            'default' => 0,
            'rules'   => [
                ['digits']
            ]
        ]
    ];

    public function run() {

        if (!$this->cms_user->is_logged) {
            return $this->cms_template->renderJSON([]);
        }

        $target_controller = $this->request->get('target_controller');
        $target_subject    = $this->request->get('target_subject');
        $target_id         = $this->request->get('target_id');

        if (!cmsCore::isControllerExists($target_controller)) {
            return $this->cms_template->renderJSON([]);
        }

        $controller = cmsCore::getController($target_controller, $this->request);

        if (!$controller->isEnabled()) {
            return $this->cms_template->renderJSON([]);
        }

        $data = $controller->runHook('wysiwyg_links_list', [$target_subject, $target_id]);

        if (!$data || $data === $this->request->getData()) {
            return $this->cms_template->renderJSON([]);
        }

        return $this->cms_template->renderJSON($data);
    }

}
