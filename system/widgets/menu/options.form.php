<?php

class formWidgetMenuOptions extends cmsForm {

    public function init($options, $template_name) {

        return array(
            'menu_options' => array(
                'type'   => 'fieldset',
                'title'  => LANG_OPTIONS,
                'childs' => array(
                    new fieldList('options:menu', array(
                        'title'     => LANG_MENU,
                        'generator' => function() {

                            $tree = cmsCore::getModel('menu')->getMenus();

                            $items = [];

                            if ($tree) {
                                foreach ($tree as $item) {
                                    $items[$item['name']] = $item['title'];
                                }
                            }

                            return $items;
                        }
                    )),
                    new fieldList('options:template', array(
                        'title'     => LANG_WD_MENU_TEMPLATE,
                        'hint'      => LANG_WD_MENU_TEMPLATE_HINT,
                        'default'   => 'menu',
                        'generator' => function($item) use ($template_name) {
                            return cmsTemplate::getInstance()->getAvailableTemplatesFiles('assets/ui', 'menu*.tpl.php', $template_name);
                        }
                    )),
                    new fieldString('options:class', array(
                        'title' => LANG_WD_MENU_CSS_CLASS
                    )),
                    new fieldCheckbox('options:is_detect', array(
                        'title'   => LANG_WD_MENU_DETECT_ACTIVE,
                        'default' => 1
                    )),
                    new fieldCheckbox('options:is_detect_strict', array(
                        'title'   => LANG_WD_MENU_ACTIVE_STRICT,
                        'hint'   => LANG_WD_MENU_ACTIVE_STRICT_HINT,
                        'default' => 0,
                        'visible_depend' => array('options:is_detect' => array('show' => array('1')))
                    )),
                    new fieldNumber('options:max_items', array(
                        'title'   => LANG_WD_MENU_MAX_ITEMS,
                        'hint'    => LANG_WD_MENU_MAX_ITEMS_HINT,
                        'default' => 0
                    ))
                )
            )
        );

    }

}
