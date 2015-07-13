<?php

class actionAdminCtypesFieldsOptions extends cmsAction {

    public function run(){

        if (!$this->request->isAjax()) { cmsCore::error404(); }

        $ctype_name = $this->request->get('ctype_name');
        $field_id   = $this->request->get('field_id');
        $field_type = $this->request->get('type');

        cmsForm::loadFormFields();

        $field_class = 'field' . string_to_camel('_',  $field_type );

        $base_field = new $field_class(null, null);

        $options = $base_field->getOptions();

        if (!$options) { $this->halt(); }

        $values = false;

        if ($ctype_name && $field_id){

            $content_model = cmsCore::getModel('content');

            $field = $content_model->getContentField($ctype_name, $field_id);

            $values = $field['options'];

        }

        cmsTemplate::getInstance()->render('ctypes_field_options', array(
            'options' => $options,
            'values' => $values
        ));

    }

}
