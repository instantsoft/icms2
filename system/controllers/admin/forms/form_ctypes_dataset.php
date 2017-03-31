<?php

//
// Эта форма не выводится в шаблон, она используется только
// для валидации нескольких базовых полей.
//
// Верстка формы сделана вручную в шаблоне ctypes_dataset.tpl
//

class formAdminCtypesDataset extends cmsForm {

    public function init($do, $ctype_id) {

        return array(
            'basic' => array(
                'type' => 'fieldset',
                'childs' => array(
                    new fieldString('name', array(
                        'rules' => array(
                            array('required'),
                            array('sysname'),
                            $do == 'add' ? array('unique_ctype_dataset', $ctype_id) : false
                        )
                    )),
                    new fieldString('title', array(
                        'rules' => array(
                            array('required'),
                            array('max_length', 100)
                        )
                    )),
                    new fieldHtml('description', array(
                        'options' => array('editor' => cmsConfig::get('default_editor'))
                    )),
                    new fieldNumber('max_count', array(
                        'default' => 0,
                        'rules' => array(
                            array('max', 65535)
                        )
                    )),
                    new fieldText('seo_title', array(
                        'options'=>array(
                            'max_length'=> 256
                        )
                    )),
                    new fieldString('seo_keys', array(
                        'options'=>array(
                            'max_length'=> 256
                        )
                    )),
                    new fieldText('seo_desc', array(
                        'options'=>array(
                            'max_length'=> 256
                        )
                    )),
                    new fieldCheckbox('is_visible', array(
                        'default' => true
                    )),
                    new fieldListGroups('groups_view', array(
                        'show_all' => true,
                        'show_guests' => true
                    )),
                    new fieldListGroups('groups_hide', array(
                        'show_all' => false,
                        'show_guests' => true
                    )),
                    new fieldList('cats_view', array(
                        'is_chosen_multiple' => true
                    )),
                    new fieldList('cats_hide', array(
                        'is_chosen_multiple' => true
                    ))
                )
            ),
        );

    }

}
