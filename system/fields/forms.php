<?php

class fieldForms extends cmsFormField {

    public $title                = LANG_PARSER_FORMS;
    public $sql                  = 'int(11) UNSIGNED NULL DEFAULT NULL';
    public $is_public            = true;
    public $var_type             = 'integer';
    public $allow_index          = false;
    public $excluded_controllers = ['forms'];

    private static $forms_data = [];

    public function __construct($name, $options = false) {
        cmsCore::loadControllerLanguage('forms');
        parent::__construct($name, $options);
    }

    public function getOptions() {
        return [
            new fieldCheckbox('form_in_modal', [
                'title' => LANG_FORMS_CP_FORM_IN_MODAL
            ]),
            new fieldString('form_in_modal_btn_title', [
                'title'          => LANG_FORMS_CP_FORM_IN_MODAL_BTN_TITLE,
                'visible_depend' => ['options:form_in_modal' => ['show' => ['1']]]
            ]),
            new fieldString('form_in_modal_btn_class', [
                'title'          => LANG_FORMS_CP_FORM_IN_MODAL_BTN_CLASS,
                'default'        => 'btn-primary',
                'visible_depend' => ['options:form_in_modal' => ['show' => ['1']]]
            ]),
            new fieldString('form_in_modal_btn_icon', [
                'title' => LANG_FORMS_CP_FORM_IN_MODAL_BTN_ICON,
                'suffix' => '<a href="#" class="icms-icon-select" data-href="'.href_to('admin', 'settings', ['theme', cmsConfig::get('template'), 'icon_list']).'"><span>'.(defined('LANG_CP_ICON_SELECT') ? LANG_CP_ICON_SELECT : '').'</span></a>',
            ]),
            new fieldCheckbox('show_title', [
                'title' => LANG_SHOW_TITLE
            ]),
            new fieldString('continue_link', [
                'title' => LANG_FORMS_CP_CONTINUE_LINK
            ]),
            new fieldList('default_form_id', [
                'title' => LANG_FORMS_CP_FORM_DEFAULT,
                'hint' => LANG_FORMS_CP_FORM_DEFAULT_HINT,
                'default'   => '',
                'generator' => function () {
                    return ['' => ''] + array_collection_to_list(cmsCore::getModel('forms')->get('forms'), 'id', 'title');
                }
            ])
        ];
    }

    public function parse($value) {

        $default_form_id = $this->getOption('default_form_id');

        if (!$value) {

            if(!$default_form_id){
                return '';
            }

            $value = $default_form_id;
        }

        $forms = cmsCore::getController('forms');

        if(!isset(self::$forms_data[$value])){
            self::$forms_data[$value] = $forms->getFormData($value);
        }

        $_form_data = self::$forms_data[$value];

        if ($_form_data === false) {
            return '';
        }

        list($form, $form_data) = $_form_data;

        $form_data['options']['show_title']    = $this->getOption('show_title');
        $form_data['options']['continue_link'] = $this->getOption('continue_link') ?: $form_data['options']['continue_link'];

        $submited_data = $forms->getSavedUserFormData($form_data['id']);

        if ($submited_data && !empty($form_data['options']['hide_after_submit'])) {
            return '';
        }

        if (!empty($this->item['user_id'])) {
            $form = $forms->setItemAuthor($form, $this->item['user_id']);
        }

        if (!empty($this->item['ctype_name']) && !empty($this->item['id'])) {

            $form = $forms->setContextTarget($form, $this->item['ctype_name'].':'.$this->item['id']);
        }

        return cmsTemplate::getInstance()->renderInternal($forms, 'form_view', [
            'modal_btn' => [
                'is_show' => $this->getOption('form_in_modal', false),
                'class'   => $this->getOption('form_in_modal_btn_class', ''),
                'icon'    => $this->getOption('form_in_modal_btn_icon', ''),
                'title'   => $this->getOption('form_in_modal_btn_title', $this->title),
            ],
            'form_data' => $form_data,
            'form'      => $form
        ]);
    }

    public function getStringValue($value) {
        return '';
    }

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
