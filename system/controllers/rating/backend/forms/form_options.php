<?php

class formRatingOptions extends cmsForm {

    public function init() {

        return array(

            array(
                'type' => 'fieldset',
                'childs' => array(

                    new fieldCheckbox('is_hidden', array(
                        'title' => LANG_RATING_IS_HIDDEN
                    )),

                    new fieldCheckbox('is_show', array(
                        'title' => LANG_RATING_SHOW_INFO
                    )),

                    new fieldCheckbox('allow_guest_vote', array(
                        'title' => LANG_RATING_ALLOW_GUEST_VOTE
                    )),

                    new fieldList('template', array(
                        'title' => LANG_RATING_TEMPLATE,
                        'hint'  => sprintf(LANG_WIDGET_BODY_TPL_HINT, 'controllers/rating/widget*'),
                        'generator' => function($item) {
                            return cmsTemplate::getInstance()->getAvailableTemplatesFiles('controllers/rating', 'widget*.tpl.php');
                        }
                    ))

                )
            )

        );

    }

}
