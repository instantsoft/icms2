<?php

class formWidgetContentSliderOptions extends cmsForm {

    public function init() {

        return array(

            array(
                'type' => 'fieldset',
                'title' => LANG_OPTIONS,
                'childs' => array(

                    new fieldList('options:ctype_id', array(
                        'title' => LANG_CONTENT_TYPE,
                        'generator' => function($item) {

                            $model = cmsCore::getModel('content');
                            $tree = $model->getContentTypes();

                            $items = array();

                            if ($tree) {
                                foreach ($tree as $item) {
                                    $items[$item['id']] = $item['title'];
                                }
                            }

                            return $items;

                        }
                    )),

                    new fieldString('options:dataset', array(
                        'title' => LANG_WD_CONTENT_SLIDER_DATASET
                    )),

                    new fieldString('options:image_field', array(
                        'title' => LANG_WD_CONTENT_SLIDER_IMAGE,
                        'rules' => array(
                            array('required')
                        )
                    )),

                    new fieldString('options:teaser_field', array(
                        'title' => LANG_WD_CONTENT_SLIDER_TEASER
                    )),

                    new fieldNumber('options:delay', array(
                        'title' => LANG_WD_CONTENT_SLIDER_DELAY,
                        'hint' => LANG_WD_CONTENT_SLIDER_DELAY_HINT,
                        'default' => 5,
                        'units' => LANG_SECOND10
                    )),

                    new fieldNumber('options:limit', array(
                        'title' => LANG_LIST_LIMIT,
                        'default' => 4,
                        'rules' => array(
                            array('required')
                        )
                    )),

                )
            ),

        );

    }

}
