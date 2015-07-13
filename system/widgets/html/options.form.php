<?php

class formWidgetHtmlOptions extends cmsForm {

    public function init() {

        return array(

            array(
                'type' => 'fieldset',
                'title' => LANG_OPTIONS, 
                'childs' => array(

                    new fieldText('options:content', array(
                        'title' => LANG_WD_HTML_CONTENT,
                        'rules' => array(
                            array('required')
                        )
                    )),

                )
            ),

        );

    }

}
