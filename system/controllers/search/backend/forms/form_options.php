<?php

class formSearchOptions extends cmsForm {

    public function init() {

        return [
            [
                'type'   => 'fieldset',
                'title'  => LANG_SEARCH_IN_CTYPES,
                'childs' => [

                    new fieldList('types', [
                        'is_multiple' => true,
                        'generator'   => function () {

                            $search_controllers = cmsEventsManager::hookAll('fulltext_search', cmsCore::getController('search'), []);

                            $items = [];

                            foreach ($search_controllers as $controller) {

                                $items = array_merge($items, $controller['sources']);
                            }

                            return $items;
                        },
                        'multiple_select_deselect' => true
                    ]),

                    new fieldCheckbox('is_hash_tag', [
                        'title' => LANG_SEARCH_IS_HASH_TAG
                    ]),

                    new fieldList('order_by', [
                        'title' => LANG_SORTING,
                        'default' => 'fsort',
                        'items' => [
                            'fsort' => LANG_SORTING_BYREL,
                            'date_pub' => LANG_SORTING_BYDATE
                        ]
                    ])
                ]
            ],
            [
                'type'   => 'fieldset',
                'title'  => LANG_SEARCH_PERPAGE,
                'childs' => [
                    new fieldNumber('perpage', [
                        'default' => 15,
                        'rules'   => [
                            ['required'],
                            ['min', 1]
                        ]
                    ])
                ]
            ]
        ];
    }

}
