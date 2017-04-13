<?php

class formPhotosOptions extends cmsForm {

    public $is_tabbed = true;

    public function init() {

        $presets = cmsCore::getModel('images')->getPresetsList();

        foreach ($presets as $name => $title) {
            $perm_childs[] = new fieldListGroups('download_view:'.$name, array(
                'title'       => sprintf(LANG_PHOTOS_DOWNLOAD_VIEW, $title),
                'show_all'    => true,
                'show_guests' => true
            ));
            $perm_childs[] = new fieldListGroups('download_hide:'.$name, array(
                'title'       => sprintf(LANG_PHOTOS_DOWNLOAD_HIDE, $title),
                'show_guests' => true
            ));
        }
        $perm_childs[] = new fieldListGroups('download_view:original', array(
            'title'       => sprintf(LANG_PHOTOS_DOWNLOAD_VIEW, LANG_PARSER_IMAGE_SIZE_ORIGINAL),
            'show_all'    => true,
            'show_guests' => true
        ));
        $perm_childs[] = new fieldListGroups('download_hide:original', array(
            'title'       => sprintf(LANG_PHOTOS_DOWNLOAD_HIDE, LANG_PARSER_IMAGE_SIZE_ORIGINAL),
            'show_guests' => true
        ));

        return array(

            array(
                'title'  => LANG_OPTIONS,
                'type'   => 'fieldset',
                'childs' => array(

                    new fieldListMultiple('sizes', array(
                        'title'   => LANG_PHOTOS_SIZES,
                        'default' => array('big', 'normal', 'small'),
                        'items'   => $presets,
                        'rules'   => array(
                            array('required')
                        )
                    )),

                    new fieldCheckbox('is_origs', array(
                        'title'   => LANG_PHOTOS_SAVE_ORIG,
                        'hint'    => LANG_PHOTOS_SAVE_ORIG_HINT,
                        'default' => 1
                    )),

                    new fieldList('preset', array(
                        'title'   => LANG_PHOTOS_PRESET,
                        'default' => 'big',
                        'items'   => $presets,
                        'rules'   => array(
                            array('required')
                        )
                    )),

                    new fieldList('preset_small', array(
                        'title'   => LANG_PHOTOS_PRESET_SMALL,
                        'default' => 'normal',
                        'items'   => $presets,
                        'rules'   => array(
                            array('required')
                        )
                    )),

                    new fieldList('preset_related', array(
                        'title'   => LANG_PHOTOS_PRESET_RELATED,
                        'default' => 'normal',
                        'items'   => $presets,
                        'rules'   => array(
                            array('required')
                        )
                    )),

                    new fieldText('types', array(
                        'title'   => LANG_PHOTOS_TYPES,
                        'hint'    => LANG_PHOTOS_TYPES_HINT,
                        'default' => "1 | Фото\n2 | Векторы\n3 | Иллюстрации",
                        'size'    => 8
                    )),

                    new fieldList('ordering', array(
                        'title'   => LANG_SORTING,
                        'default' => 'date_pub',
                        'items'   => modelPhotos::getOrderList()
                    )),

                    new fieldList('orderto', array(
                        'title'   => LANG_PHOTOS_SORT_ORDERTO,
                        'default' => 'desc',
                        'items'   => array(
                            'asc'  => LANG_SORTING_ASC,
                            'desc' => LANG_SORTING_DESC
                        )
                    )),

                    new fieldNumber('limit', array(
                        'title'   => LANG_LIST_LIMIT,
                        'default' => 16,
                        'rules'   => array(
                            array('required')
                        )
                    )),

                    new fieldNumber('related_limit', array(
                        'title'   => LANG_PHOTOS_RELATED_LIMIT,
                        'default' => 20,
                        'rules'   => array(
                            array('required')
                        )
                    )),

                    new fieldString('url_pattern', array(
                        'title'   => LANG_PHOTOS_URL_PATTERN,
                        'prefix'  => '/photos/',
                        'suffix'  => '.html',
                        'default' => '{id}-{title}',
                        'rules' => array(
                            array('required')
                        )
                    ))

                )
            ),

            array(
                'title'  => LANG_PERMISSIONS,
                'type'   => 'fieldset',
                'childs' => $perm_childs
            )

        );

    }

}
