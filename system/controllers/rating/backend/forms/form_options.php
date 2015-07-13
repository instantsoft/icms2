<?php

class formRatingOptions extends cmsForm {

    public function init() {

        return array(

            array(
                'type' => 'fieldset',
                'childs' => array(

                    new fieldCheckbox('is_hidden', array(
                        'title' => LANG_RATING_IS_HIDDEN,
                    )),

                    new fieldCheckbox('is_show', array(
                        'title' => LANG_RATING_SHOW_INFO,
                    )),

                )
            ),

        );

    }

}
