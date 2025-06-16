<?php

class formBillingVipfield extends cmsForm {

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
                    new fieldString('description', [
                        'title' => LANG_BILLING_CP_VIP_FIELD_DESC,
                        'rules' => [
                            ['required'],
                            ['max_length', 255]
                        ]
                    ])
                ]
            ],
            'prices' => [
                'title'  => LANG_BILLING_CP_VIP_FIELD_PRICES,
                'type'   => 'fieldset',
                'childs' => $prices
            ]
        ];
    }

}
