<?php

class formAuthRestore extends cmsForm {

    public function init() {

        return array(

            'basic' => array(
                'type' => 'fieldset',
                'title' => LANG_EMAIL,
                'childs' => array(
                    new fieldString('email', array(
                        'rules' => array(
                            array('required'),
                            array('email')
                        )
                    ))
                )
            )

        );

    }

}
