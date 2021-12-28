<?php

class widgetSubscriptionsButton extends cmsWidget {

    public $is_cacheable = false;

    public function run() {

        if (strpos(cmsCore::getInstance()->uri, '.html') === false) {
            return false;
        }

        $show_btn_title = $this->getOption('show_btn_title', true);
        if($show_btn_title === null){ $show_btn_title = false; }

        $buttons = [];
        $current_user_id = cmsUser::get('id');

        $ctype = cmsModel::getCachedResult('current_ctype');

        if ($ctype) {

            $category = [];

            $item = cmsModel::getCachedResult('current_ctype_item');
            if ($item) {
                if (!empty($item['category'])) {
                    $category = $item['category'];
                }
            }

            $subscriptions = cmsCore::getController('subscriptions');

            if (!$this->getOption('hide_all')){

                $buttons[] = [
                    'title'  => $this->getOption('hide_all_title') ? '' : LANG_ALL . ' ' . mb_strtolower($ctype['title']),
                    'button' => $subscriptions->renderSubscribeButton([
                        'controller' => 'content',
                        'subject'    => $ctype['name'],
                        'params'     => []
                    ], $show_btn_title)
                ];
            }

            if (!$this->getOption('hide_user') && $item && $current_user_id != $item['user_id']) {

                $buttons[] = [
                    'title'  => $this->getOption('hide_user_title') ? '' : $ctype['title'] . ' ' . LANG_FROM . ' ' . $item['user']['nickname'],
                    'button' => $subscriptions->renderSubscribeButton([
                        'controller' => 'content',
                        'subject'    => $ctype['name'],
                        'params'     => [
                            'filters' => [
                                [
                                    'field'     => 'user_id',
                                    'condition' => 'eq',
                                    'value'     => $item['user_id']
                                ]
                            ]
                        ]
                    ], $show_btn_title)
                ];
            }

            if (!$this->getOption('hide_cat') && !empty($category['id']) && $category['id'] > 1) {

                $buttons[] = [
                    'title'  => $this->getOption('hide_cat_title') ? '' : $ctype['title'] . '/' . $category['title'],
                    'button' => $subscriptions->renderSubscribeButton([
                        'controller' => 'content',
                        'subject'    => $ctype['name'],
                        'params'     => [
                            'filters' => [
                                [
                                    'field'     => 'category_id',
                                    'condition' => 'eq',
                                    'value'     => (string) $category['id']
                                ]
                            ]
                        ]
                    ], $show_btn_title)
                ];

                if (!$this->getOption('hide_user') && $item && $current_user_id != $item['user_id']) {

                    $buttons[] = [
                        'title'  => $this->getOption('hide_user_title') ? '' : $ctype['title'] . '/' . $category['title'] . ' ' . LANG_FROM . ' ' . $item['user']['nickname'],
                        'button' => $subscriptions->renderSubscribeButton([
                            'controller' => 'content',
                            'subject'    => $ctype['name'],
                            'params'     => [
                                'filters' => [
                                    [
                                        'field'     => 'category_id',
                                        'condition' => 'eq',
                                        'value'     => (string) $category['id']
                                    ],
                                    [
                                        'field'     => 'user_id',
                                        'condition' => 'eq',
                                        'value'     => $item['user_id']
                                    ]
                                ]
                            ]
                        ], $show_btn_title)
                    ];
                }
            }
        }

        $photo_data = cmsModel::getCachedResult('current_photo_item');

        if (!$this->getOption('hide_album') && $photo_data) {

            list($album, $photo) = $photo_data;

            $subscriptions = cmsCore::getController('subscriptions');

            if ($current_user_id != $photo['user_id']) {

                $buttons[] = [
                    'title'  => $this->getOption('hide_album_title') ? '' : $album['title'],
                    'button' => $subscriptions->renderSubscribeButton([
                        'controller' => 'photos',
                        'subject'    => 'album',
                        'params'     => [
                            'filters' => [
                                [
                                    'field'     => 'album_id',
                                    'condition' => 'eq',
                                    'value'     => $photo['album_id']
                                ]
                            ]
                        ]
                    ], $show_btn_title)
                ];
            }
        }


        if (!$buttons) { return false; }

        return [
            'buttons' => $buttons
        ];
    }

}
