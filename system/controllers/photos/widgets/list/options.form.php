<?php

class formWidgetPhotosListOptions extends cmsForm {

    public function init() {

        $photo = cmsCore::getController('photos');

        return array(

            array(
                'type' => 'fieldset',
                'title' => LANG_OPTIONS,
                'childs' => array(

                    new fieldCheckbox('options:auto_user', array(
                        'title'   => LANG_PHOTOS_O_AUTO_USER,
                        'hint'    => LANG_PHOTOS_O_AUTO_USER_HINT,
                        'default' => 1
                    )),

                    new fieldCheckbox('options:auto_group', array(
                        'title'   => LANG_CP_WO_AUTO_GROUP,
                        'hint'    => LANG_CP_WO_AUTO_GROUP_HINT,
                        'default' => 1
                    )),

                    new fieldCheckbox('options:auto_ctype_item', array(
                        'title'   => LANG_PHOTOS_O_AUTO_CTYPE_ITEM,
                        'hint'    => LANG_PHOTOS_O_AUTO_CTYPE_ITEM_HINT,
                        'default' => 1
                    )),

                    new fieldList('options:target', array(
                        'title' => LANG_PHOTOS_O_TARGET,
                        'items' => array(
                            LANG_ALL, LANG_PHOTOS_PUBLIC_ALBUMS, LANG_PHOTOS_USER_ALBUMS
                        ),
                        'default' => 0
                    )),

                    new fieldList('options:album_id', array(
                        'title' => LANG_PHOTOS_ALBUM,
                        'generator' => function (){
                            $_items = cmsCore::getModel('content')->limit(false)->
                                    disablePrivacyFilter()->getContentItemsForSitemap('albums', array('id', 'title'));
                            $items = array('' => '');
                            if ($_items) {
                                foreach ($_items as $item) {
                                    $items[$item['id']] = $item['title'];
                                }
                            }
                            return $items;
                        },
                        'default' => ''
                    )),

                    new fieldList('options:ordering', array(
                        'title'   => LANG_SORTING,
                        'items'   => array('' => '') + modelPhotos::getOrderList(),
                        'default' => ''
                    )),

                    new fieldList('options:orientation', array(
                        'title'   => LANG_PHOTOS_O_ORIENTATION,
                        'items'   => modelPhotos::getOrientationList(),
                        'default' => ''
                    )),

                    new fieldList('options:type', array(
                        'title'   => LANG_PHOTOS_O_TYPE,
                        'items'   => (!empty($photo->options['types']) ? (array('' => LANG_PHOTOS_ALL) + $photo->options['types']) : array()),
                        'default' => ''
                    )),

                    new fieldNumber('options:width', array(
                        'title'   => LANG_PHOTOS_SIZE_W.', '.  mb_strtolower(LANG_PHOTOS_MORE_THAN),
                        'units'   => 'px',
                        'default' => ''
                    )),

                    new fieldNumber('options:height', array(
                        'title'   => LANG_PHOTOS_SIZE_H.', '.  mb_strtolower(LANG_PHOTOS_MORE_THAN),
                        'units'   => 'px',
                        'default' => ''
                    )),

                    new fieldNumber('options:limit', array(
                        'title' => LANG_LIST_LIMIT,
                        'default' => 10,
                        'rules' => [
                            ['required'],
                            ['min', 1]
                        ]
                    ))

                )
            )

        );

    }

}
