<?php

class formAuthVerify extends cmsForm {

    public function init() {

        return array(

            'basic' => array(
                'type' => 'fieldset',
                'title' => LANG_VERIFY_EMAIL_CODE,
                'childs' => array(
                    new fieldString('reg_token', array(
                        'rules' => array(
                            array('required'),
                            array('max_length', 32),
                            array('min_length', 32)
                        )
                    ))
                )
            )

        );

    }

}
