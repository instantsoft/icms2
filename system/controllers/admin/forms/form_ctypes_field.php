<?php
class formAdminCtypesField extends cmsForm {

    public function init($do, $ctype_name) {

        $model = cmsCore::getModel('content');

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
                            $do == 'add' ? array('unique_ctype_field', $ctype_name) : false
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
                            return cmsForm::getAvailableFormFields('only_public', 'content');
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
                            $fieldsets = $model->getContentFieldsets($field['ctype_id']);
                            $items = array('');
                            foreach($fieldsets as $fieldset) { $items[$fieldset] = $fieldset; }
                            return $items;
                        }
                    )),
                    new fieldString('new_fieldset', array(
                        'title' => LANG_CP_FIELD_FIELDSET_ADD,
                        'rules' => array(
                            array('max_length', 32)
                        )
                    )),
                )
            ),
            'visibility' => array(
                'type' => 'fieldset',
                'title' => LANG_CP_FIELD_VISIBILITY,
                'childs' => array(
                    new fieldCheckbox('is_in_item', array(
                        'title' => LANG_CP_FIELD_IN_ITEM,
                        'default' => true
                    )),
                    new fieldCheckbox('is_in_list', array(
                        'title' => LANG_CP_FIELD_IN_LIST,
                    )),
                    new fieldListMultiple('options:context_list', array(
                        'title' => LANG_CP_FIELD_IN_LIST_CONTEXT,
                        'default'   => 0,
                        'show_all'  => true,
                        'is_vertical' => true,
                        'generator' => function() use($ctype_name) {

                            $lists = cmsEventsManager::hookAll('ctype_lists_context', 'template:'.$ctype_name);

                            $items = array();

                            if($lists){
                                foreach ($lists as $list) {
                                    $items = array_merge($items, $list);
                                }
                            }

                            return $items;

                        }
                    )),
                    new fieldCheckbox('is_in_filter', array(
                        'title' => LANG_CP_FIELD_IN_FILTER,
                    )),
                    new fieldList('options:relation_id', array(
                        'title' => LANG_CP_FIELD_IN_RELATION,
                        'generator' => function() use ($model, $ctype_name) {

                            $ctype = $model->getContentTypeByName($ctype_name);

                            $parents = $model->getContentTypeParents($ctype['id']);

                            $items = array('0' => LANG_NO);

                            if (is_array($parents)){
                                foreach($parents as $parent){
                                    $items[$parent['id']] = "{$ctype['title']} > {$parent['ctype_title']}";
                                };
                            }

                            return $items;

                        }
                    ))
                )
            ),
            'labels' => array(
                'type' => 'fieldset',
                'title' => LANG_CP_FIELD_LABELS,
                'childs' => array(
                    new fieldList('options:label_in_list', array(
                        'title' => LANG_CP_FIELD_LABELS_IN_LIST,
                        'default' => 'left',
                        'items' => array(
                            'left' => LANG_CP_FIELD_LABEL_LEFT,
                            'top' => LANG_CP_FIELD_LABEL_TOP,
                            'none' => LANG_CP_FIELD_LABEL_NONE
                        )
                    )),
                    new fieldList('options:label_in_item', array(
                        'title' => LANG_CP_FIELD_LABELS_IN_ITEM,
                        'default' => 'left',
                        'items' => array(
                            'left' => LANG_CP_FIELD_LABEL_LEFT,
                            'top' => LANG_CP_FIELD_LABEL_TOP,
                            'none' => LANG_CP_FIELD_LABEL_NONE
                        )
                    )),
                )
            ),
            'wrap' => array(
                'type' => 'fieldset',
                'title' => LANG_CP_FIELD_WRAP,
                'childs' => array(
                    new fieldList('options:wrap_type', array(
                        'title' => LANG_CP_FIELD_WRAP_TYPE,
                        'default' => 'auto',
                        'items' => array(
                            'left'  => LANG_CP_FIELD_WRAP_LTYPE,
                            'right' => LANG_CP_FIELD_WRAP_RTYPE,
                            'none'  => LANG_CP_FIELD_WRAP_NTYPE,
                            'auto'  => LANG_CP_FIELD_WRAP_ATYPE
                        )
                    )),
                    new fieldString('options:wrap_width', array(
                        'title'   => LANG_CP_FIELD_WRAP_WIDTH,
                        'hint'    => LANG_CP_FIELD_WRAP_WIDTH_HINT,
                        'default' => ''
                    ))
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
                    )),
                    new fieldCheckbox('options:is_unique', array(
                        'title' => LANG_VALIDATE_UNIQUE,
                    )),
                )
            ),
            'values' => array(
                'type' => 'fieldset',
                'title' => LANG_CP_FIELD_VALUES,
                'childs' => array(
                    new fieldText('values', array(
                        'size' => 8
                    ))
                )
            ),
            'profile' => array(
                'type' => 'fieldset',
                'title' => LANG_CP_FIELD_PROFILE_VALUE,
                'childs' => array(
                    new fieldList('options:profile_value', array(
                        'hint' => LANG_CP_FIELD_PROFILE_VALUE_HINT,
                        'generator' => function($field) use($model){
                            $model->setTablePrefix(''); // Ниже модель не используется
                            $fields = $model->filterIn('type', array('string', 'text', 'html', 'list', 'city'))->getContentFields('{users}');
                            $items = array(''=>LANG_NO) + array_collection_to_list($fields, 'name', 'title');
                            return $items;
                        }
                    ))
                )
            ),
            'read_access' => array(
                'type' => 'fieldset',
                'title' => LANG_CP_FIELD_GROUPS_READ,
                'childs' => array(
                    new fieldListGroups('groups_read', array(
                        'show_all' => true
                    ))
                )
            ),
            'add_access' => array(
                'type' => 'fieldset',
                'title' => LANG_CP_FIELD_GROUPS_ADD,
                'childs' => array(
                    new fieldListGroups('groups_add', array(
                        'show_all' => true
                    ))
                )
            ),
            'edit_access' => array(
                'type' => 'fieldset',
                'title' => LANG_CP_FIELD_GROUPS_EDIT,
                'childs' => array(
                    new fieldListGroups('groups_edit', array(
                        'show_all' => true
                    ))
                )
            ),
            'filter_access' => array(
                'type' => 'fieldset',
                'title' => LANG_CP_FIELD_IN_FILTER,
                'childs' => array(
                    new fieldListGroups('filter_view', array(
                        'show_all' => true
                    ))
                )
            ),
            'author_access' => array(
                'type' => 'fieldset',
                'title' => LANG_CP_FIELD_AUTHOR_ACCESS,
                'childs' => array(
                    new fieldListMultiple('options:author_access', array(
                        'items' => array(
                            'is_read' => LANG_CP_FIELD_READING,
                            'is_edit' => LANG_CP_FIELD_EDITING,
                        )
                    ))
                )
            )
        );

    }

}