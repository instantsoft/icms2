<?php
class widgetFormsForm extends cmsWidget {

    public $is_cacheable = false;

    public $insert_controller_css = true;

    public function run(){

        $form_id = $this->getOption('form_id');
        if (!$form_id) {
            return false;
        }

        $forms = cmsCore::getController('forms');

        $_form_data = $forms->getFormData($form_id);

        if ($_form_data === false) {
            return false;
        }

        list($form, $form_data) = $_form_data;

        $form_data['options']['show_title'] = $this->getOption('show_title');

        $submited_data = $forms->getSavedUserFormData($form_data['id']);

        if($submited_data && !empty($form_data['options']['hide_after_submit'])){
            // @todo
            // сделать показ данных формы, если отправлена авторизованным
            return false;
        }

        return [
            'form_data' => $form_data,
            'form'      => $form
        ];

    }

}
