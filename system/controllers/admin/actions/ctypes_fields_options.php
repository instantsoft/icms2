<?php

class actionAdminCtypesFieldsOptions extends cmsAction {

    public function run() {

        if (!$this->request->isAjax()) {
            return cmsCore::error404();
        }

        $ctype_name = $this->request->get('ctype_name', '');
        $field_id   = $this->request->get('field_id', 0);
        $field_type = $this->request->get('type', '');
        $form_id    = $this->request->get('form_id', '');

        if (!$field_type || !$ctype_name) {
            return $this->halt();
        }

        $field_class = 'field' . string_to_camel('_', $field_type);
        if (!class_exists($field_class)) {
            return cmsCore::error404();
        }

        $base_field = new $field_class(null, null);

        $base_field->subject_name = $ctype_name;

        $options = $base_field->getOptions();

        if (!$options) {
            return $this->cms_template->renderJSON([
                'error' => false,
                'html'  => false
            ]);
        }

        $values = [
            'options' => []
        ];

        if ($field_id) {

            if (!$this->model_backend_content->getContentTypeByName($ctype_name)) {
                $this->model_backend_content->setTablePrefix('');
            }

            $field = $this->model_backend_content->localizedOff()->getContentField($ctype_name, $field_id);
            if (!$field) {
                return $this->halt();
            }

            $this->model_backend_content->localizedRestore();

            $values['options'] = $field['options'];
        }

        $form = $this->makeForm(function($form) use($options){

            $form->addFieldset(LANG_CP_FIELD_TYPE_OPTS, 'field_settings');

            foreach ($options as $field_field) {

                $field_field->setName("options:{$field_field->name}");

                $form->addField('field_settings', $field_field);
            }

            return $form;
        });

        ob_start();

        $options_js_file = $this->cms_template->getJavascriptFileName('fields/' . $field_type);
        if ($options_js_file) {
            $this->cms_template->addJSFromContext($options_js_file);
        }

        $this->cms_template->renderForm($form, $values, [
            'form_id'       => $form_id,
            'form_tpl_file' => 'form_fields'
        ]);

        return $this->cms_template->renderJSON([
            'error'            => false,
            'is_can_in_filter' => ($base_field->filter_type !== false),
            'is_virtual'       => $base_field->is_virtual,
            'html'             => ob_get_clean()
        ]);
    }

}
