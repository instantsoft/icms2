<?php

class widgetFormsForm extends cmsWidget {

    public $is_cacheable = false;
    public $insert_controller_css = true;

    public function run() {

        $form_id = $this->getOption('form_id');
        if (!$form_id) {
            return false;
        }

        $forms = cmsCore::getController('forms');

        $_form_data = $forms->getFormData($form_id);

        if ($_form_data === false) {
            return false;
        }

        $ctype = cmsModel::getCachedResult('current_ctype') ?: [];
        $item  = cmsModel::getCachedResult('current_ctype_item') ?: [];

        list($form, $form_data) = $_form_data;

        $form_data['options']['show_title'] = $this->getOption('show_title');

        $submited_data = $forms->getSavedUserFormData($form_data['id']);

        if (!empty($item['user_id'])) {
            $form = $forms->setItemAuthor($form, $item['user_id']);
        }

        if ($ctype && !empty($item['id'])) {
            $form = $forms->setContextTarget($form, $ctype['name'] . ':' . $item['id']);
        }

        if ($submited_data && !empty($form_data['options']['hide_after_submit'])) {
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
