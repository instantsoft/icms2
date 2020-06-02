<?php

class formWidgetContentFieldsOptions extends cmsForm {

    public function init($options = false) {

        $fields_list = ['' => ''];

        if (!empty($options['ctype_id'])) {

            $content_model = cmsCore::getModel('content');

            $ctype = $content_model->getContentType($options['ctype_id']);

            $fields = $content_model->getContentFields($ctype['name']);
            if ($fields) {
                foreach ($fields as $field) {
                    if($field['is_system']){
                        continue;
                    }
                    $fields_list[$field['name']] = $field['title'];
                }
            }
        }

        return array(
            array(
                'type'   => 'fieldset',
                'title'  => LANG_OPTIONS,
                'childs' => array(
                    new fieldList('options:ctype_id', array(
                        'title' => LANG_CONTENT_TYPE,
                        'hint' => LANG_WD_CONTENT_FIELDS_CT_HINT,
                        'generator' => function($item) {

                            $model = cmsCore::getModel('content');
                            $tree  = $model->getContentTypes();

                            $items = ['' => ''];

                            if ($tree) {
                                foreach ($tree as $item) {
                                    $items[$item['id']] = $item['title'];
                                }
                            }

                            return $items;
                        },
                        'rules' => array(
                            array('required')
                        )
                    )),
                    new fieldList('options:image_field', array(
                        'title'  => LANG_WD_CONTENT_FIELDS_IF,
                        'parent' => array(
                            'list' => 'options:ctype_id',
                            'url'  => href_to('content', 'widget_fields_ajax').'?'.http_build_query(['filter' => [
                                'is_system' => 1,
                                'options' => [
                                    'is_in_item_pos' => ''
                                ]
                            ]])
                        ),
                        'items' => $fields_list
                    )),
                    new fieldList('options:image_preset', array(
                        'title' => LANG_WD_CONTENT_FIELDS_IFP,
                        'generator' => function($item) {
                            return cmsCore::getModel('images')->getPresetsList(true)+array('original' => LANG_PARSER_IMAGE_SIZE_ORIGINAL);
                        },
                        'visible_depend' => array('options:image_field' => array('hide' => array('')))
                    )),
                    new fieldCheckbox('options:image_is_parallax', array(
                        'title' => LANG_WD_CONTENT_FIELDS_IFPA,
                        'visible_depend' => array('options:image_field' => array('hide' => array('')))
                    )),
                    new fieldList('options:fields', array(
                        'title' => LANG_CP_CTYPE_FIELDS,
                        'is_chosen_multiple' => true,
                        'parent' => array(
                            'list' => 'options:ctype_id',
                            'url'  => href_to('content', 'widget_fields_ajax').'?'.http_build_query(['filter' => [
                                'is_system' => 1,
                                'options' => [
                                    'is_in_item_pos' => ''
                                ]
                            ]])
                        ),
                        'items' => $fields_list,
                        'rules' => array(
                            array('required')
                        )
                    ))
                )
            )
        );

    }

}
