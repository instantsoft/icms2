<?php
class widgetFormsForm extends cmsWidget {

    public $is_cacheable = false;

    public $insert_controller_css = true;

    public function run(){

        $form_id = $this->getOption('form_id');
        if (!$form_id) {
            return false;
        }

        $model = cmsCore::getModel('forms');

        $_form_data = $model->getFormData($form_id);

        if ($_form_data === false) {
            return false;
        }

        list($form, $form_data) = $_form_data;

        $form_data['options']['show_title'] = $this->getOption('show_title');
        $form_data['options']['continue_link'] = $this->getOption('continue_link') ?: $form_data['options']['continue_link'];

        return array(
            'form_data' => $form_data,
            'form'      => $form
        );

    }

}
