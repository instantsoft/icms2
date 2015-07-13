<?php
class formAdminMenu extends cmsForm {

    public function init($do) {

        return array(
            'basic' => array(
                'type' => 'fieldset',
                'childs' => array(
                    new fieldString('name', array(
                        'title' => LANG_SYSTEM_NAME,
                        'rules' => array(
                            array('required'),
                            array('sysname'),
                            $do == 'add' ? array('unique', 'menu', 'name') : false
                        )
                    )),
                    new fieldString('title', array(
                        'title' => LANG_TITLE,
                        'rules' => array(
                            array('required'),
                            array('max_length', 64)
                        )
                    )),
                )
            )
        );

    }

}