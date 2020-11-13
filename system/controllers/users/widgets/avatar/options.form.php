<?php

class formWidgetUsersAvatarOptions extends cmsForm {

    public function init() {

        cmsCore::loadControllerLanguage('users');

        return array(

            array(
                'type' => 'fieldset',
                'title' => LANG_OPTIONS,
                'childs' => array(

                    new fieldList('options:menu', array(
                        'title' => LANG_MENU,
                        'generator' => function() {

                            $menu_model = cmsCore::getModel('menu');
                            $tree = $menu_model->getMenus();

                            $items = array();

                            if ($tree) {
                                foreach ($tree as $item) {
                                    $items[$item['name']] = $item['title'];
                                }
                            }

                            return $items;

                        }
                    )),

                    new fieldCheckbox('options:is_detect', array(
                        'title' => LANG_WD_AVATAR_DETECT_ACTIVE,
                        'default' => 1
                    )),

                    new fieldNumber('options:max_items', array(
                        'title' => LANG_WD_AVATAR_MAX_ITEMS,
                        'hint' => LANG_WD_AVATAR_MAX_ITEMS_HINT,
                        'default' => 0
                    )),

                )
            ),

        );

    }

}
