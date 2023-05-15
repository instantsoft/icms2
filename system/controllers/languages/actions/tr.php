<?php

class actionLanguagesTr extends cmsAction {

    public $request_params = [
        // Текст для перевода
        'q' => [
            'default' => '',
            'rules' => [
                ['required'],
                ['max_length', 5000]
            ]
        ],
        // Язык, с которого переводим
        'sl' => [
            'default' => '',
            'rules' => [
                ['required'],
                ['min_length', 2],
                ['max_length', 4]
            ]
        ],
        // Язык, в который переводим
        'tl' => [
            'default' => '',
            'rules' => [
                ['required'],
                ['min_length', 2],
                ['max_length', 4]
            ]
        ]
    ];

    public function run() {

        if (!$this->cms_user->is_logged) {
            return cmsCore::error404();
        }

        if (!$this->request->isAjax()) {
            return cmsCore::error404();
        }

        $q  = $this->request->get('q');
        $sl = $this->request->get('sl');
        $tl = $this->request->get('tl');

        return $this->cms_template->renderJSON([
            'error' => false,
            'translate' => $this->requestTranslation($sl, $tl, $q)
        ]);
    }

    private function requestTranslation($source, $target, $text) {

        if(empty($this->options['service'])){
            return '';
        }

        $service_class = 'icms\controllers\languages\services\\' . $this->options['service'];

        $service = new $service_class();

        return $service->translate($source, $target, $text);
    }

}
