<?php

class formAdminConfirmLogin extends cmsForm {

    public function init() {

        return array(

            'basic' => array(
                'type' => 'fieldset',
                'title' => LANG_PASSWORD,
                'childs' => array(
                    new fieldString('password', array(
                        'is_password' => true,
                        'rules' => array(
                            array('required'),
                            array('min_length', 6)
                        )
                    ))
                )
            )

        );

    }

}
