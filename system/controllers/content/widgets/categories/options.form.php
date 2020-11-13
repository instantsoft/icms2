<?php

class formWidgetContentCategoriesOptions extends cmsForm {

    public function init($options = false) {

        $presets = cmsCore::getModel('images')->getPresetsList();
        $presets['original'] = LANG_PARSER_IMAGE_SIZE_ORIGINAL;

		if (!empty($options['ctype_name'])){

            $ctype = cmsCore::getModel('content')->getContentTypeByName($options['ctype_name']);
            if ($ctype) {

                $_presets = array();

                if ($presets && !empty($ctype['options']['cover_sizes'])){
                    foreach($presets as $key => $name){
                        if(in_array($key, $ctype['options']['cover_sizes'])){
                            $_presets[$key] = $name;
                        }
                    }
                }

                $presets = $_presets ? $_presets : $presets;

            }

		}

        return array(

            array(
                'type' => 'fieldset',
                'title' => LANG_CONTENT_TYPE,
                'childs' => array(

                    new fieldList('options:ctype_name', array(
                        'generator' => function($c) {

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

                    new fieldList('options:cover_preset', array(
                        'title' => LANG_CP_CAT_CONTEXT_LIST_COVER_SIZES,
                        'items' => ['' => ''] + $presets,
						'parent' => array(
							'list' => 'options:ctype_name',
							'url' => href_to('content', 'widget_cats_presets_ajax')
						)
                    ))

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
