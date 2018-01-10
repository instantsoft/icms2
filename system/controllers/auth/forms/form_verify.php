<?php

class formAuthVerify extends cmsForm {

    public function init() {

        return array(

            'basic' => array(
                'type' => 'fieldset',
                'title' => LANG_VERIFY_EMAIL_CODE,
                'childs' => array(
                    new fieldString('reg_token', array(
                        'options'=>array(
                            'min_length'=> 32,
                            'max_length'=> 32
                        ),
                        'rules' => array(
                            array('required')
                        )
                    ))
                )
            )

        );

    }

}
