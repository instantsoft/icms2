<?php
class formFormsField extends cmsForm {

    public function init($do, $form_id) {

        $model = cmsCore::getModel('forms');

        return array(
            'basic' => array(
                'type' => 'fieldset',
                'childs' => array(
                    new fieldString('name', array(
                        'title' => LANG_SYSTEM_NAME,
						'hint' => $do=='edit' ? LANG_SYSTEM_EDIT_NOTICE : false,
                        'rules' => array(
                            array('required'),
                            array('sysname'),
                            array('max_length', 40),
                            $do == 'add' ? array('unique_field', $form_id) : false
                        )
                    )),
                    new fieldString('title', array(
                        'title' => LANG_CP_FIELD_TITLE,
                        'rules' => array(
                            array('required'),
                            array('max_length', 100)
                        )
                    )),
                    new fieldString('hint', array(
                        'title' => LANG_CP_FIELD_HINT,
                        'is_clean_disable' => true,
                        'rules' => array(
                            array('max_length', 255)
                        )
                    )),
                    new fieldCheckbox('is_enabled', array(
                        'title' => LANG_IS_ENABLED,
                        'default' => 1
                    ))
                )
            ),
            'type' => array(
                'type' => 'fieldset',
                'title' => LANG_CP_FIELD_TYPE,
                'childs' => array(
                    new fieldList('type', array(
                        'default' => 'string',
                        'generator' => function() {
                            return cmsForm::getAvailableFormFields('only_public', 'forms');
                        }
                    ))
                )
            ),
            'group' => array(
                'type' => 'fieldset',
                'title' => LANG_CP_FIELD_FIELDSET,
                'childs' => array(
                    new fieldList('fieldset', array(
                        'title' => LANG_CP_FIELD_FIELDSET_SELECT,
                        'generator' => function($field) use ($model){
                            $fieldsets = $model->getFormFieldsets($field['form_id']);
                            $items = array('');
                            foreach($fieldsets as $fieldset) { $items[$fieldset] = $fieldset; }
                            return $items;
                        }
                    )),
                    new fieldString('new_fieldset', array(
                        'title' => LANG_CP_FIELD_FIELDSET_ADD,
                        'rules' => array(
                            array('max_length', 100)
                        )
                    )),
                )
            ),
            'format' => array(
                'type' => 'fieldset',
                'title' => LANG_CP_FIELD_FORMAT,
                'childs' => array(
                    new fieldCheckbox('options:is_required', array(
                        'title' => LANG_VALIDATE_REQUIRED,
                    )),
                    new fieldCheckbox('options:is_digits', array(
                        'title' => LANG_VALIDATE_DIGITS,
                    )),
                    new fieldCheckbox('options:is_alphanumeric', array(
                        'title' => LANG_VALIDATE_ALPHANUMERIC,
                    )),
                    new fieldCheckbox('options:is_email', array(
                        'title' => LANG_VALIDATE_EMAIL,
                    )),
                    new fieldCheckbox('options:is_url', array(
                        'title' => LANG_VALIDATE_URL,
                    ))
                )
            ),
            'values' => array(
                'type' => 'fieldset',
                'title' => LANG_CP_FIELD_VALUES,
                'childs' => array(
                    new fieldText('values', array(
                        'size' => 8,
                        'is_strip_tags' => true
                    ))
                )
            )
        );

    }

}
