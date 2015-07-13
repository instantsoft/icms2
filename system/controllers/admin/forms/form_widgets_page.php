<?php
class formAdminWidgetsPage extends cmsForm {

    public function init() {

        return array(
            'title' => array(
                'type' => 'fieldset',
                'childs' => array(
                    new fieldString('title', array(
                        'title' => LANG_TITLE,
                        'rules' => array(
                            array('required'),
                            array('max_length', 64)
                        )
                    )),
                )
            ),
            'urls' => array(
                'type' => 'fieldset',
                'title' => LANG_CP_WIDGET_PAGE_URLS,
                'childs' => array(

                    new fieldText('url_mask', array(
                        'title' => LANG_CP_WIDGET_PAGE_URL_MASK,
                        'rules' => array(
                            array('required')
                        )
                    )),
                    new fieldText('url_mask_not', array(
                        'title' => LANG_CP_WIDGET_PAGE_URL_MASK_NOT,
                    )),
                    
                )
            ),
        );

    }

}
