<?php

class formWidgetContentFieldsOptions extends cmsForm {

    public function init($options = false) {

        $content_model = cmsCore::getModel('content');

        $field_generator = function ($item, $request) use($content_model) {
            $list     = ['' => ''];
            $ctype_id = is_array($item) ? array_value_recursive('options:ctype_id', $item) : false;
            if (!$ctype_id && $request) {
                $ctype_id = $request->get('options:ctype_id', 0);
            }
            if (!$ctype_id) {
                return $list;
            }
            $ctype = $content_model->getContentType($ctype_id);
            if (!$ctype) {
                return $list;
            }
            $fields = $content_model->getContentFields($ctype['name']);

            if ($fields) {
                foreach ($fields as $field) {
                    if($field['is_system']){
                        continue;
                    }
                    if (!in_array('widget', $field['options']['is_in_item_pos'])) {
                        continue;
                    }
                    $list[$field['name']] = $field['title'];
                }
            }
            return $list;
        };

        return array(
            array(
                'type'   => 'fieldset',
                'title'  => LANG_OPTIONS,
                'childs' => array(
                    new fieldList('options:ctype_id', array(
                        'title' => LANG_CONTENT_TYPE,
                        'hint' => LANG_WD_CONTENT_FIELDS_CT_HINT,
                        'generator' => function($ctype) {

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
                    new fieldImage('options:image_path', array(
                        'title'  => LANG_WD_FIELDS_IMG,
                        'hint'    => LANG_WD_FIELDS_IMG_HINT,
                        'options' => array(
                            'sizes' => array(
                                'original'
                            )
                        )
                    )),
                    new fieldList('options:image_field', array(
                        'title' => LANG_WD_CONTENT_FIELDS_IF,
                        'hint'  => LANG_WD_CONTENT_FIELDS_IF_HINT,
                        'parent' => array(
                            'list' => 'options:ctype_id',
                            'url'  => href_to('content', 'widget_fields_ajax').'?'.http_build_query(['excluding_filter' => [
                                'is_system' => 1,
                                'options' => [
                                    'is_in_item_pos' => ['page']
                                ]
                            ]])
                        ),
                        'generator' => $field_generator
                    )),
                    new fieldList('options:image_preset', array(
                        'title' => LANG_WD_CONTENT_FIELDS_IFP,
                        'generator' => function($item) {
                            return cmsCore::getModel('images')->getPresetsList(true)+array('original' => LANG_PARSER_IMAGE_SIZE_ORIGINAL);
                        },
                        'visible_depend' => array('options:image_field' => array('hide' => array('')))
                    )),
                    new fieldCheckbox('options:image_is_parallax', array(
                        'title' => LANG_WD_CONTENT_FIELDS_IFPA
                    )),
                    new fieldList('options:fields', array(
                        'title' => LANG_CP_CTYPE_FIELDS,
                        'is_chosen_multiple' => true,
                        'parent' => array(
                            'list' => 'options:ctype_id',
                            'url'  => href_to('content', 'widget_fields_ajax').'?'.http_build_query(['excluding_filter' => [
                                'is_system' => 1,
                                'options' => [
                                    'is_in_item_pos' => ['page']
                                ]
                            ]])
                        ),
                        'generator' => $field_generator,
                        'rules' => array(
                            array('required')
                        )
                    )),
                    new fieldCheckbox('options:show_info_block', array(
                        'title' => LANG_WD_CONTENT_FIELDS_SHOW_INFO_BLOCK
                    ))
                )
            )
        );

    }

}
