<?php

class formWidgetContentSliderOptions extends cmsForm {

    public function init($options=false) {

		$cats_list     = array('0' => '');
        $datasets_list = array('0' => '');
        $fields_list   = array('' => '');

        if (!empty($options['ctype_id'])){

			$content_model = cmsCore::getModel('content');

			$ctype = $content_model->getContentType($options['ctype_id']);
            $cats  = $content_model->getCategoriesTree($ctype['name']);

            if ($cats){
				foreach($cats as $cat){
					if ($cat['ns_level'] > 1){
						$cat['title'] = str_repeat('-', $cat['ns_level']) . ' ' . $cat['title'];
					}
					$cats_list[$cat['id']] = $cat['title'];

				}
			}

			$datasets = $content_model->getContentDatasets($options['ctype_id']);
			if ($datasets){ $datasets_list = array(0=>'') + array_collection_to_list($datasets, 'id', 'title'); }

			$fields = $content_model->getContentFields($ctype['name']);
			if ($fields){ $fields_list = array(''=>'') + array_collection_to_list($fields, 'name', 'title'); }

		}

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

                        },
                    )),

					new fieldList('options:category_id', array(
						'title' => LANG_CATEGORY,
						'parent' => array(
							'list' => 'options:ctype_id',
							'url' => href_to('content', 'widget_cats_ajax')
						),
						'items' => $cats_list
					)),

                    new fieldList('options:dataset', array(
                        'title' => LANG_WD_CONTENT_SLIDER_DATASET,
						'parent' => array(
							'list' => 'options:ctype_id',
							'url' => href_to('content', 'widget_datasets_ajax')
						),
						'items' => $datasets_list
                    )),

                    new fieldList('options:image_field', array(
                        'title' => LANG_WD_CONTENT_SLIDER_IMAGE,
                        'rules' => array(
                            array('required')
                        ),
						'parent' => array(
							'list' => 'options:ctype_id',
							'url' => href_to('content', 'widget_fields_ajax')
						),
						'items' => $fields_list
                    )),

                    new fieldList('options:big_image_field', array(
                        'title' => LANG_WD_CONTENT_SLIDER_BIG_IMAGE,
                        'hint' => LANG_WD_CONTENT_SLIDER_BIG_IMAGE_HINT,
						'parent' => array(
							'list' => 'options:ctype_id',
							'url' => href_to('content', 'widget_fields_ajax')
						),
						'items' => $fields_list
                    )),

                    new fieldList('options:big_image_preset', array(
                        'title' => LANG_WD_CONTENT_SLIDER_BIG_IMAGE_PRESET,
                        'generator' => function($item) {
                            return cmsCore::getModel('images')->getPresetsList(true)+array('original' => LANG_PARSER_IMAGE_SIZE_ORIGINAL);
                        },
                    )),

                    new fieldList('options:teaser_field', array(
                        'title' => LANG_WD_CONTENT_SLIDER_TEASER,
						'parent' => array(
							'list' => 'options:ctype_id',
							'url' => href_to('content', 'widget_fields_ajax')
						),
						'items' => $fields_list
                    )),

                    new fieldNumber('options:teaser_len', array(
                        'title' => LANG_PARSER_HTML_TEASER_LEN,
                        'hint' => LANG_PARSER_HTML_TEASER_LEN_HINT,
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
                    ))

                )
            )

        );

    }

}
