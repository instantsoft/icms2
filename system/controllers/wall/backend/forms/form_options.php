<?php

class formWallOptions extends cmsForm {

    public function init() {

        return array(

            array(
                'type'  => 'fieldset',
                'title' => LANG_BASIC_OPTIONS,
                'childs' => array(

                    new fieldNumber('limit', array(
                        'title' => LANG_LIST_LIMIT,
                        'default' => 15,
                        'rules' => array(
                            array('required')
                        )
                    )),

                    new fieldNumber('show_entries', array(
                        'title' => LANG_WALL_SHOW_ENTRIES,
                        'default' => 5,
                        'rules' => array(
                            array('required')
                        )
                    )),

                    new fieldList('order_by', array(
                        'title' => LANG_SORTING,
                        'default' => 'date_pub',
                        'rules' => array(
                            array('required')
                        ),
                        'items' => array(
                            'date_pub' => LANG_DATE_PUB,
                            'date_last_reply' => LANG_WALL_SORTING_DATE_LAST_REPLY
                        )
                    )),

                    new fieldList('editor', array(
                        'title' => LANG_PARSER_HTML_EDITOR,
                        'default' => cmsConfig::get('default_editor'),
                        'generator' => function($item){
                            $items = ['' => 'Textarea'];
                            $editors = cmsCore::getWysiwygs();
                            foreach($editors as $editor){
                                $items[$editor] = ucfirst($editor);
                            }
                            $ps = cmsCore::getModel('wysiwygs')->getPresetsList();
                            if($ps){
                                foreach ($ps as $key => $value) {
                                    $items[$key] = $value;
                                }
                            }
                            return $items;
                        }
                    )),

                    new fieldList('editor_presets', array(
                        'title'        => LANG_PARSER_HTML_EDITOR_GR,
                        'is_multiple'  => true,
                        'dynamic_list' => true,
                        'select_title' => LANG_SELECT,
                        'multiple_keys' => array(
                            'group_id' => 'field', 'preset_id' => 'field_select'
                        ),
                        'generator' => function($item){
                            $users_model = cmsCore::getModel('users');

                            $items = [];

                            $groups = $users_model->getGroups(false);

                            foreach($groups as $group){
                                $items[$group['id']] = $group['title'];
                            }

                            return $items;
                        },
                        'values_generator' => function() {
                            $items = ['' => 'Textarea'];
                            $editors = cmsCore::getWysiwygs();
                            foreach($editors as $editor){
                                $items[$editor] = ucfirst($editor);
                            }
                            $ps = cmsCore::getModel('wysiwygs')->getPresetsList();
                            if($ps){
                                foreach ($ps as $key => $value) {
                                    $items[$key] = $value;
                                }
                            }
                            return $items;
                        }
                    ))

                )
            )

        );

    }

}
