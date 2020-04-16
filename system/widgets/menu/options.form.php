<?php

class formWidgetMenuOptions extends cmsForm {

    public function init($options, $template_name) {

        return array(

            array(
                'type' => 'fieldset',
                'title' => LANG_OPTIONS,
                'childs' => array(

                    new fieldList('options:menu', array(
                        'title' => LANG_MENU,
                        'generator' => function($item) {

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

                    new fieldList('options:template', array(
                        'title' => LANG_WD_MENU_TEMPLATE,
                        'hint'  => LANG_WD_MENU_TEMPLATE_HINT,
                        'default' => 'menu',
                        'generator' => function($item) use ($template_name) {
                            return cmsTemplate::getInstance()->getAvailableTemplatesFiles('assets/ui', 'menu*.tpl.php', $template_name);
                        }
                    )),

                    new fieldString('options:class', array(
                        'title' => LANG_WD_MENU_CSS_CLASS
                    )),

                    new fieldCheckbox('options:is_detect', array(
                        'title' => LANG_WD_MENU_DETECT_ACTIVE,
                        'default' => 1
                    )),

                    new fieldNumber('options:max_items', array(
                        'title' => LANG_WD_MENU_MAX_ITEMS,
                        'hint' => LANG_WD_MENU_MAX_ITEMS_HINT,
                        'default' => 0
                    ))

                )
            )

        );

    }

}
