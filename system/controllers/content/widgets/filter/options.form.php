<?php

class formWidgetContentFilterOptions extends cmsForm {

    public function init() {

        return array(

            array(
                'type' => 'fieldset',
                'title' => LANG_CONTENT_TYPE,
                'childs' => array(

                    new fieldList('options:ctype_name', array(
                        'generator' => function($ctype) {

                            $model = cmsCore::getModel('content');
                            $tree = $model->getContentTypes();

                            $items = array(0 => LANG_WD_CONTENT_FILTER_DETECT);

                            if ($tree) {
                                foreach ($tree as $item) {
                                    $items[$item['name']] = $item['title'];
                                }
                            }

                            return $items;

                        }
                    )),

                )
            ),

        );

    }

}
