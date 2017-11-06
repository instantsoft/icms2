<?php

class formWidgetContentCategoriesOptions extends cmsForm {

    public function init() {

        return array(

            array(
                'type' => 'fieldset',
                'title' => LANG_CONTENT_TYPE,
                'childs' => array(

                    new fieldList('options:ctype_name', array(
                        'generator' => function($item) {

                            $model = cmsCore::getModel('content');
                            $tree = $model->getContentTypes();

                            $items = array(0 => LANG_WD_CONTENT_CATS_DETECT);

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

            array(
                'type' => 'fieldset',
                'title' => LANG_OPTIONS,
                'childs' => array(

                    new fieldCheckbox('options:is_root', array(
                        'title' => LANG_WD_CONTENT_CATS_SHOW_ROOT,
                        'default' => false
                    )),

                    new fieldCheckbox('options:show_full_tree', array(
                        'title' => LANG_WD_CONTENT_CATS_SHOW_FULL_TREE,
                        'default' => false
                    ))

                )
            ),

        );

    }

}
