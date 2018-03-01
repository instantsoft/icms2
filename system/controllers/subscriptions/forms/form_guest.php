<?php

class formSubscriptionsGuest extends cmsForm {

    public function init() {

        return array(

            'basic' => array(
                'type' => 'fieldset',
                'childs' => array(
                    new fieldString('email', array(
                        'title' => LANG_EMAIL,
                        'options'=>array(
                            'max_length'=> 100
                        ),
                        'rules' => array(
                            array('required'),
                            array('email')
                        )
                    )),
                    new fieldString('name', array(
                        'title' => LANG_NAME,
                        'options'=>array(
                            'max_length'=> 50
                        ),
                        'rules' => array(
                            array('required'),
                            array('regexp', '/^([0-9a-zа-яёй\.\@\,\ \-]+)$/ui')
                        )
                    ))
                )
            )

        );

    }

}
