<?php

class actionFormsFieldsOptions extends cmsAction {

    public function run(){

        if (!$this->request->isAjax()) { cmsCore::error404(); }

        $field_id   = $this->request->get('field_id', 0);
        $field_type = $this->request->get('type', '');
        $form_id    = $this->request->get('form_id', '');

        if(!$field_type){ $this->halt(); }

        $field_class = 'field' . string_to_camel('_',  $field_type );
        if(!class_exists($field_class)){ cmsCore::error404(); }

        $base_field = new $field_class(null, null);

        $options = $base_field->getOptions();

        if(!$options){
            return $this->cms_template->renderJSON(array(
                'error' => false,
                'html'  => false
            ));
        }

        $values = [
            'options' => []
        ];

        if($field_id){

            $field = $this->model->getFormField($field_id);
            if(!$field){ $this->halt(); }

            $values['options'] = $field['options'];
        }

        $form = new cmsForm();

        $form->addFieldset(LANG_CP_FIELD_TYPE_OPTS, 'field_settings');

        foreach ($options as $key => $field_field) {

            $name = "options:{$field_field->name}";

            $field_field->setName($name);

            $form->addField('field_settings', $field_field);
        }

        ob_start();

        $options_js_file = $this->cms_template->getJavascriptFileName('fields/'.$field_type);
        if($options_js_file){
            $this->cms_template->addJSFromContext($options_js_file);
        }

        $this->cms_template->renderForm($form, $values, [
            'form_id' => $form_id,
            'form_tpl_file' => 'form_fields'
        ]);

        return $this->cms_template->renderJSON(array(
            'error' => false,
            'html'  => ob_get_clean()
        ));
    }

}
