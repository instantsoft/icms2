<?php

class formMessagesOptions extends cmsForm {

    public function init() {

        return array(

            array(
                'type' => 'fieldset',
                'childs' => array(

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

                            $groups = $users_model->getGroups();

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
                    )),

                    new fieldNumber('limit', array(
                        'title'   => LANG_PM_LIMIT,
                        'default' => 5,
                        'rules' => array(
                            array('required'),
                            array('min', 1)
                        )
                    )),

                    new fieldNumber('time_delete_old', array(
                        'title'   => LANG_PM_TIME_DELETE_OLD,
                        'hint'    => LANG_PM_TIME_DELETE_OLD_HINT,
                        'default' => 0,
                        'units'   => LANG_DAY10
                    )),

                    new fieldList('realtime_mode', array(
                        'title' => LANG_PM_REALTIME_MODE,
                        'items' => array(
                            'ajax'   => 'Ajax',
                            'socket' => LANG_PM_REALTIME_MODE_SOCKET
                        )
                    )),

                    new fieldNumber('refresh_time', array(
                        'title'   => LANG_PM_REFRESH_TIME,
                        'default' => 15,
                        'units'   => LANG_SECOND10,
                        'rules' => array(
                            array('required'),
                            array('min', 1)
                        ),
                        'visible_depend' => array('realtime_mode' => array('show' => array('ajax')))
                    )),

                    new fieldString('socket_host', array(
                        'title' => LANG_PM_REALTIME_SOCKET_HOST,
                        'visible_depend' => array('realtime_mode' => array('show' => array('socket')))
                    )),

                    new fieldNumber('socket_port', array(
                        'title'   => LANG_PM_REALTIME_SOCKET_PORT,
                        'default' => 3000,
                        'rules' => array(
                            array('min', 1)
                        ),
                        'visible_depend' => array('realtime_mode' => array('show' => array('socket')))
                    )),

                    new fieldCheckbox('use_queue', array(
                        'title' => LANG_PM_USE_QUEUE
                    ))

                )
            ),

            array(
                'type' => 'fieldset',
                'title' => LANG_PERMISSIONS,
                'childs' => array(

                    new fieldListGroups('groups_allowed', array(
                        'show_all' => true,
                        'default'  => array(0)
                    ))

                )
            )

        );

    }

}
