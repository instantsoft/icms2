<?php
class formContentWidgetContentList extends cmsForm {

    public function init($ctype, $fields) {

        cmsCore::loadControllerLanguage('admin');
        cmsCore::loadWidgetLanguage('list', 'content');

        $childs = [
            new cmsFormField('fake', array(
                    'title' => '',
                    'hint'  => LANG_WD_CONTENT_LIST_FIELDS_HINT,
                    'html'  => ''
                )
            )
        ];

        foreach ($fields as $field) {

            if ($field['is_system']) { continue; }

            $name = "options:show_fields:{$ctype['id']}:{$field['name']}";

            $childs[] = new fieldCheckbox($name, array(
                    'title' => $field['title']
                )
            );

            $options = $field['handler']->getOptionsExtended();

            if($options){
                foreach ($options as $option_field) {

                    $option_field->setName('options:show_fields_options:'.$ctype['id'].':'.$field['name'].':'.$option_field->getName());
                    $option_field->setProperty('visible_depend', [$name => array('show' => array('1'))]);

                    $childs[] = $option_field;
                }
            }

            $childs[] = new fieldList('options:show_fields_options:'.$ctype['id'].':'.$field['name'].':label_in_list', array(
                'title' => LANG_CP_FIELD_LABELS,
                'default' => 'none',
                'visible_depend' => [$name => array('show' => array('1'))],
                'items' => array(
                    'left' => LANG_CP_FIELD_LABEL_LEFT,
                    'top' => LANG_CP_FIELD_LABEL_TOP,
                    'none' => LANG_CP_FIELD_LABEL_NONE
                )
            ));

            $childs[] = new fieldList('options:show_fields_options:'.$ctype['id'].':'.$field['name'].':wrap_type', array(
                'title' => LANG_CP_FIELD_WRAP.': '.LANG_CP_FIELD_WRAP_TYPE,
                'default' => 'auto',
                'visible_depend' => [$name => array('show' => array('1'))],
                'items' => array(
                    'left'  => LANG_CP_FIELD_WRAP_LTYPE,
                    'right' => LANG_CP_FIELD_WRAP_RTYPE,
                    'none'  => LANG_CP_FIELD_WRAP_NTYPE,
                    'auto'  => LANG_CP_FIELD_WRAP_ATYPE
                )
            ));
            $childs[] = new fieldString('options:show_fields_options:'.$ctype['id'].':'.$field['name'].':wrap_width', array(
                'title'   => LANG_CP_FIELD_WRAP.': '.LANG_CP_FIELD_WRAP_WIDTH,
                'hint'    => LANG_CP_FIELD_WRAP_WIDTH_HINT,
                'visible_depend' => [$name => array('show' => array('1'))],
                'default' => ''
            ));
            $childs[] = new fieldNumber('options:show_fields_options:'.$ctype['id'].':'.$field['name'].':ordering', array(
                'title'   => LANG_ORDER,
                'options' => ['save_zero' => false],
                'visible_depend' => [$name => array('show' => array('1'))],
                'default' => ''
            ));
        }

        $childs[] = new fieldCheckbox("options:show_fields:{$ctype['id']}:date_pub", array(
                'title' => LANG_DATE
            )
        );

        $childs[] = new fieldCheckbox("options:show_fields:{$ctype['id']}:user", array(
                'title' => LANG_AUTHOR
            )
        );
        $childs[] = new fieldCheckbox("options:show_fields:{$ctype['id']}:comments", array(
                'title' => LANG_COMMENTS
            )
        );

        return [
            [
                'type' => 'fieldset',
                'childs' => $childs
            ]
        ];
    }

}
