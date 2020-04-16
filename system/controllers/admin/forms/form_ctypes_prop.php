<?php
class formAdminCtypesProp extends cmsForm {

    public function init($do) {

        $model = cmsCore::getModel('content');

        return array(
            'basic' => array(
                'type' => 'fieldset',
                'childs' => array(
                    new fieldString('title', array(
                        'title' => LANG_CP_PROP_TITLE,
                        'rules' => array(
                            array('required'),
                            array('max_length', 100)
                        )
                    )),
                    new fieldCheckbox('is_in_filter', array(
                        'title' => LANG_CP_FIELD_IN_FILTER,
                    )),
                )
            ),
            'group' => array(
                'type' => 'fieldset',
                'title' => LANG_CP_FIELD_FIELDSET,
                'childs' => array(
                    new fieldList('fieldset', array(
                        'title' => LANG_CP_FIELD_FIELDSET_SELECT,
                        'generator' => function($prop) use($model){
                            $fieldsets = $model->getContentPropsFieldsets($prop['ctype_id']);
                            $items = array('');
                            if (is_array($fieldsets)){
                                foreach($fieldsets as $fieldset) { $items[$fieldset] = $fieldset; }
                            }
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
            'type' => array(
                'type' => 'fieldset',
                'title' => LANG_CP_FIELD_TYPE,
                'childs' => array(
                    new fieldList('type', array(
                        'default' => 'list',
                        'items' => array(
                            'list'          => LANG_PARSER_LIST,
                            'list_multiple' => LANG_PARSER_LIST_MULTIPLE,
                            'string'        => LANG_PARSER_STRING,
                            'color'         => LANG_PARSER_COLOR,
                            'number'        => LANG_PARSER_NUMBER,
                            'checkbox'      => LANG_PARSER_CHECKBOX
                        )
                    )),
                    new fieldCheckbox('options:is_required', array(
                        'title' => LANG_VALIDATE_REQUIRED
                    ))
                )
            ),
            'number' => array(
                'type' => 'fieldset',
                'title' => LANG_PARSER_NUMBER,
                'childs' => array(
                    new fieldString('options:units', array(
                        'title' => LANG_CP_PROP_UNITS,
                    )),
                    new fieldCheckbox('options:is_filter_range', array(
                        'title' => LANG_PARSER_NUMBER_FILTER_RANGE
                    )),
                )
            ),
            'values' => array(
                'type' => 'fieldset',
                'title' => LANG_CP_PROP_VALUES,
                'childs' => array(
                    new fieldText('values', array(
                        'size' => 8,
                        'is_strip_tags' => true,
                        'hint' => LANG_CP_PROP_VALUES_HINT
                    )),
                    new fieldCheckbox('options:is_filter_multi', array(
                        'title' => LANG_PARSER_LIST_FILTER_MULTI
                    ))
                )
            ),
            'cats' => array(
                'type' => 'fieldset',
                'title' => LANG_CP_PROP_CATS,
                'childs' => array(
                    new fieldList('cats', array(
                            'is_multiple' => true,
                            'multiple_select_deselect' => true,
                            'is_tree' => true,
                            'generator' => function($prop) use($model){
                                $ctype = $model->getContentType($prop['ctype_id']);
                                $tree = $model->limit(0)->getCategoriesTree($ctype['name'], false);
                                foreach($tree as $c){
                                    $items[$c['id']] = str_repeat('- ', $c['ns_level']).' '.$c['title'];
                                }
                                return $items;
                            }
                        )
                    )
                )
            ),
        );

    }

}