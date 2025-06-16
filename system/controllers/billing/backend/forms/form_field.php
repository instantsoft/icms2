<?php

class formBillingField extends cmsForm {

    public function init() {

        $groups = cmsCore::getModel('users')->getGroups();

        $prices = [];

        foreach ($groups as $g) {

            $prices[] = new fieldNumber("prices:{$g['id']}", [
                'title'   => $g['title'],
                'default' => 0.0
            ]);
        }

        $content_model = cmsCore::getModel('content');

        $field_generator = function ($item, $request) use ($content_model) {
            $list     = ['' => ''];
            $ctype_id = $item['ctype_id'] ?? 0;
            if (!$ctype_id && $request) {
                $ctype_id = $request->get('ctype_id', 0);
            }
            if (!$ctype_id) {
                return $list;
            }
            $ctype = $content_model->getContentType($ctype_id);
            if (!$ctype) {
                return $list;
            }
            $fields = $content_model->getContentFields($ctype['name']);
            if ($fields) {
                $list += array_collection_to_list($fields, 'name', 'title');
            }
            return $list;
        };

        return [
            'target' => [
                'title'  => LANG_CP_BASIC,
                'type'   => 'fieldset',
                'childs' => [
                    new fieldList('ctype_id', [
                        'title'     => LANG_CONTENT_TYPE,
                        'generator' => function ($item) use($content_model) {
                            $tree  = $content_model->getContentTypes();
                            $items = ['' => ''];
                            if ($tree) {
                                $items += array_collection_to_list($tree, 'id', 'title');
                            }
                            return $items;
                        },
                        'rules' => [
                            ['required']
                        ]
                    ]),
                    new fieldList('field', [
                        'title'     => LANG_BILLING_CP_FIELD_NAME,
                        'parent'    => [
                            'list' => 'ctype_id',
                            'url'  => href_to('content', 'widget_fields_ajax')
                        ],
                        'generator' => $field_generator,
                        'rules' => [
                            ['required']
                        ]
                    ]),
                    new fieldList('price_field', [
                        'title' => LANG_BILLING_CP_FIELD_PRICE_FIELD,
                        'hint'  => LANG_BILLING_CP_FIELD_PRICE_FIELD_HINT,
                        'parent'    => [
                            'list' => 'ctype_id',
                            'url'  => href_to('content', 'widget_fields_ajax')
                        ],
                        'generator' => $field_generator
                    ]),
                    new fieldList('is_to_author', [
                        'title' => LANG_BILLING_CP_FIELD_IS_TO_AUTHOR,
                        'items' => [
                            0 => LANG_BILLING_CP_FIELD_IS_TO_AUTHOR_0,
                            1 => LANG_BILLING_CP_FIELD_IS_TO_AUTHOR_1
                        ]
                    ]),
                    new fieldString('notify_email', [
                        'title' => LANG_BILLING_CP_FIELD_NOTIFY_EMAIL,
                        'hint'  => LANG_BILLING_CP_FIELD_NOTIFY_EMAIL_HINT,
                        'rules' => [
                            ['email']
                        ]
                    ]),
                    new fieldCheckbox('is_notify_author', [
                        'title' => LANG_BILLING_CP_FIELD_NOTIFY_AUTHOR,
                    ]),
                    new fieldString('btn_titles:guest', [
                        'title' => LANG_BILLING_CP_FIELD_BTN_TITLES_GUEST,
                        'can_multilanguage' => true,
                        'patterns_hint' => [
                            'patterns'     => ['price' => LANG_BILLING_CP_PRICE_SPELL],
                            'text_panel'   => LANG_BILLING_CP_FIELD_BTN_TITLES_HINT,
                            'text_pattern' => LANG_CP_SEOMETA_HINT_PATTERN
                        ]
                    ]),
                    new fieldString('btn_titles:user', [
                        'title' => LANG_BILLING_CP_FIELD_BTN_TITLES_USER,
                        'can_multilanguage' => true,
                        'patterns_hint' => [
                            'patterns'     => ['price' => LANG_BILLING_CP_PRICE_SPELL],
                            'text_panel'   => LANG_BILLING_CP_FIELD_BTN_TITLES_HINT,
                            'text_pattern' => LANG_CP_SEOMETA_HINT_PATTERN
                        ]
                    ])
                ]
            ],
            'prices' => [
                'title'  => LANG_BILLING_CP_FIELD_PRICES,
                'type'   => 'fieldset',
                'childs' => $prices
            ]
        ];
    }

}
