<?php

class formBillingTerm extends cmsForm {

    public function init() {

        $groups = cmsCore::getModel('users')->getGroups();

        $prices = [];

        foreach ($groups as $g) {

            $prices[] = new fieldNumber("prices:{$g['id']}", [
                'title'   => $g['title'],
                'default' => 0.0,
                'options' => [
                    'is_abs' => true
                ]
            ]);
        }

        return [
            'target' => [
                'title'  => LANG_CP_BASIC,
                'type'   => 'fieldset',
                'childs' => [
                    new fieldList('ctype_id', [
                        'title'     => LANG_CONTENT_TYPE,
                        'generator' => function ($item) {
                            $model = cmsCore::getModel('content');
                            $tree  = $model->getContentTypes();
                            $items = [];
                            if ($tree) {
                                $items = array_collection_to_list($tree, 'id', 'title');
                            }
                            return $items;
                        }
                    ])
                ]
            ],
            'prices' => [
                'title'  => LANG_BILLING_CP_TERM_PRICES,
                'type'   => 'fieldset',
                'childs' => $prices
            ]
        ];
    }

}
