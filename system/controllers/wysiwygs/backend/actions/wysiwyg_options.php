<?php

class actionWysiwygsWysiwygOptions extends cmsAction {

    public $request_params = array(
        'wysiwyg_name' => array(
            'default' => '',
            'rules'   => array(
                array('required'),
                array('sysname'),
                array('max_length', 40)
            )
        ),
        'preset_id' => array(
            'default' => 0,
            'rules'   => array(
                array('digits')
            )
        )
    );

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

        $wysiwyg_name = $this->request->get('wysiwyg_name');

        $form = $this->getWysiwygOptionsForm($wysiwyg_name, ($preset ? ['edit'] : ['add']));

        ob_start();

        $this->cms_template->renderForm($form, $preset, [
            'form_tpl_file' => 'form_fields'
        ]);

        return $this->cms_template->renderJSON(array(
            'error' => false,
            'html'  => ob_get_clean()
        ));

    }

}
