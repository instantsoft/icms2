<?php

class fieldForms extends cmsFormField {

    public $title     = LANG_PARSER_FORMS;
    public $sql       = 'int(11) UNSIGNED NULL DEFAULT NULL';
    public $is_public = true;
    public $var_type  = 'integer';
    public $allow_index = false;
    public $excluded_controllers = ['forms'];

	public function __construct($name, $options = false){
        cmsCore::loadControllerLanguage('forms');
        parent::__construct($name, $options);
    }

    public function getOptions(){
        return array(
            new fieldCheckbox('show_title', array(
                'title' => LANG_SHOW_TITLE
            )),
            new fieldString('continue_link', array(
                'title' => LANG_FORMS_CP_CONTINUE_LINK
            ))
        );
    }

    public function parse($value) {

        if(!$value){ return ''; }

        $forms = cmsCore::getController('forms');

        $_form_data = $forms->getFormData($value);

        if ($_form_data === false) {
            return '';
        }

        list($form, $form_data) = $_form_data;

        $form_data['options']['show_title'] = $this->getOption('show_title');
        $form_data['options']['continue_link'] = $this->getOption('continue_link') ?: $form_data['options']['continue_link'];

        $submited_data = $forms->getSavedUserFormData($form_data['id']);

        if($submited_data && !empty($form_data['options']['hide_after_submit'])){
            return '';
        }

        if(!empty($this->item['user_id'])){
            $form = $forms->setItemAuthor($form, $this->item['user_id']);
        }

        return cmsTemplate::getInstance()->renderInternal($forms, 'form_view', [
            'form_data' => $form_data,
            'form'      => $form
        ]);
    }

    public function parseTeaser($value){ return null; }

    public function getStringValue($value) { return null; }

    public function applyFilter($model, $value) {
        return $model->filterNotNull($this->name);
    }

    public function store($value, $is_submitted, $old_value = null) {
        return $value ?: null;
    }

    public function getInput($value) {

        $this->data['items'] = ['' => ''] + array_collection_to_list(cmsCore::getModel('forms')->get('forms'), 'id', 'title');

        return parent::getInput($value);
    }

}
