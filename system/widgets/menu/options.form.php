<?php

class formWidgetMenuOptions extends cmsForm {

    public function init($options, $template_name) {

        return [
            'menu_options' => [
                'type'   => 'fieldset',
                'title'  => LANG_OPTIONS,
                'childs' => [
                    new fieldList('options:menu', [
                        'title' => LANG_MENU,
                        'generator' => function () {

                            $tree = cmsCore::getModel('menu')->getMenus();

                            $items = [];

                            if ($tree) {
                                foreach ($tree as $item) {
                                    $items[$item['name']] = $item['title'];
                                }
                            }

                            return $items;
                        }
                    ]),
                    new fieldList('options:template', [
                        'title'     => LANG_WD_MENU_TEMPLATE,
                        'hint'      => LANG_WD_MENU_TEMPLATE_HINT,
                        'default'   => 'menu',
                        'generator' => function ($item) use ($template_name) {
                            return cmsTemplate::getInstance()->getAvailableTemplatesFiles('assets/ui', 'menu*.tpl.php', $template_name);
                        }
                    ]),
                    new fieldString('options:class', [
                        'title' => LANG_WD_MENU_CSS_CLASS
                    ]),
                    new fieldCheckbox('options:is_detect', [
                        'title'   => LANG_WD_MENU_DETECT_ACTIVE,
                        'default' => 1
                    ]),
                    new fieldCheckbox('options:is_detect_strict', [
                        'title'          => LANG_WD_MENU_ACTIVE_STRICT,
                        'hint'           => LANG_WD_MENU_ACTIVE_STRICT_HINT,
                        'default'        => 0,
                        'visible_depend' => ['options:is_detect' => ['show' => ['1']]]
                    ]),
                    new fieldNumber('options:max_items', [
                        'title'   => LANG_WD_MENU_MAX_ITEMS,
                        'hint'    => LANG_WD_MENU_MAX_ITEMS_HINT,
                        'default' => 0
                    ])
                ]
            ]
        ];
    }

}
