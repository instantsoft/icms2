<?php

class formWidgetCommentsListOptions extends cmsForm {

    public function init() {

        return [
            [
                'type'   => 'fieldset',
                'title'  => LANG_OPTIONS,
                'childs' => [

                    new fieldListMultiple('options:show_list', [
                        'title' => LANG_WD_COMMENTS_SHOW_LIST,
                        'default' => 0,
                        'show_all'=> true,
                        'generator' => function($item) {

                            $items = [];

                            $comments_targets = cmsEventsManager::hookAll('comments_targets');

                            if (is_array($comments_targets)){
                                foreach($comments_targets as $comments_target){
                                    foreach($comments_target['types'] as $name => $title){
                                        $items[$name] = $title;
                                    }
                                }
                            }

                            return $items;
                        }
                    ]),

                    new fieldCheckbox('options:show_avatars', [
                        'title' => LANG_WD_COMMENTS_LIST_SHOW_AVATARS,
                        'default' => true
                    ]),

                    new fieldCheckbox('options:show_text', [
                        'title' => LANG_WD_COMMENTS_LIST_SHOW_TEXT,
                        'default' => false
                    ]),

                    new fieldCheckbox('options:show_rating', [
                        'title' => LANG_WD_COMMENTS_LIST_SHOW_RATING,
                        'default' => false
                    ]),

                    new fieldNumber('options:limit', [
                        'title' => LANG_LIST_LIMIT,
                        'default' => 10,
                        'rules' => [
                            ['required'],
                            ['min', 1]
                        ]
                    ])

                ]
            ]
        ];
    }

}
