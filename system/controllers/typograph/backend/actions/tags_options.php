<?php

class actionTypographTagsOptions extends cmsAction {

    public $request_params = [
        'tags' => [
            'default' => []
        ],
        'preset_id' => [
            'default' => 0,
            'rules'   => [
                ['digits']
            ]
        ]
    ];

    public function run(){

        if (!$this->request->isAjax()) {
            return cmsCore::error404();
        }

        $preset_id = $this->request->get('preset_id');

        $preset = [];

        if($preset_id){

            $preset = $this->model->getPreset($preset_id);

            if (!$preset) {
                return cmsCore::error404();
            }
        }

        $tags = [];

        $all_tags = $this->getHtmlTags();

        $_tags = $this->request->get('tags');

        foreach ($_tags as $t) {
            if(is_string($t) && in_array($t, $all_tags, true)){
                $tags[] = $t;
            }
        }

        if(!$tags){
            return $this->cms_template->renderJSON([
                'error' => false,
                'html'  => false
            ]);
        }

        $form = $this->getTagsForm($tags);

        ob_start();

        $this->cms_template->renderForm($form, $preset, [
            'form_tpl_file' => 'form_fields'
        ]);

        return $this->cms_template->renderJSON([
            'error' => false,
            'html'  => ob_get_clean()
        ]);
    }

}
