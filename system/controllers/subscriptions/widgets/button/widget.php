<?php
class widgetSubscriptionsButton extends cmsWidget {

    public $is_cacheable = false;

    public function run(){

        if(strpos(cmsCore::getInstance()->uri, '.html') === false){
            return false;
        }

        $buttons = array();
        $current_user_id = cmsUser::get('id');

        $ctype = cmsModel::getCachedResult('current_ctype');
        if($ctype){

            $category = array();

            $item = cmsModel::getCachedResult('current_ctype_item');
            if($item){
                if(!empty($item['category'])){
                    $category = $item['category'];
                }
            }

            $subscriptions = cmsCore::getController('subscriptions');

            $buttons[] = array(
                'title'  => LANG_ALL.' '.mb_strtolower($ctype['title']),
                'button' => $subscriptions->renderSubscribeButton(array(
                    'controller' => 'content',
                    'subject'    => $ctype['name'],
                    'params'     => array()
                ))
            );

            if($item && $current_user_id != $item['user_id']){
                $buttons[] = array(
                    'title'  => $ctype['title'].' '.LANG_FROM.' '.$item['user']['nickname'],
                    'button' => $subscriptions->renderSubscribeButton(array(
                        'controller' => 'content',
                        'subject'    => $ctype['name'],
                        'params'     => array(
                            'filters' => array(
                                array(
                                    'field'     => 'user_id',
                                    'condition' => 'eq',
                                    'value'     => $item['user_id']
                                )
                            )
                        )
                    ))
                );
            }

            if(!empty($category['id']) && $category['id'] > 1){
                $buttons[] = array(
                    'title'  => $ctype['title'].'/'.$category['title'],
                    'button' => $subscriptions->renderSubscribeButton(array(
                        'controller' => 'content',
                        'subject'    => $ctype['name'],
                        'params'     => array(
                            'filters' => array(
                                array(
                                    'field'     => 'category_id',
                                    'condition' => 'eq',
                                    'value'     => (string)$category['id']
                                )
                            )
                        )
                    ))
                );

                if($item && $current_user_id != $item['user_id']){
                    $buttons[] = array(
                        'title'  => $ctype['title'].'/'.$category['title'].' '.LANG_FROM.' '.$item['user']['nickname'],
                        'button' => $subscriptions->renderSubscribeButton(array(
                            'controller' => 'content',
                            'subject'    => $ctype['name'],
                            'params'     => array(
                                'filters' => array(
                                    array(
                                        'field'     => 'category_id',
                                        'condition' => 'eq',
                                        'value'     => (string)$category['id']
                                    ),
                                    array(
                                        'field'     => 'user_id',
                                        'condition' => 'eq',
                                        'value'     => $item['user_id']
                                    )
                                )
                            )
                        ))
                    );
                }

            }

        }

        $photo_data = cmsModel::getCachedResult('current_photo_item');
        if($photo_data){

            list($album, $photo) = $photo_data;

            $subscriptions = cmsCore::getController('subscriptions');

            if($current_user_id != $photo['user_id']){
                $buttons[] = array(
                    'title'  => $album['title'],
                    'button' => $subscriptions->renderSubscribeButton(array(
                        'controller' => 'photos',
                        'subject'    => 'album',
                        'params'     => array(
                            'filters' => array(
                                array(
                                    'field'     => 'album_id',
                                    'condition' => 'eq',
                                    'value'     => $photo['album_id']
                                )
                            )
                        )
                    ))
                );
            }

        }


        if(!$buttons){ return false; }

        return array(
			'buttons' => $buttons
        );

    }

}
