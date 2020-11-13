<?php

class formWidgetCommentsListOptions extends cmsForm {

    public function init() {

        return array(

            array(
                'type' => 'fieldset',
                'title' => LANG_OPTIONS,
                'childs' => array(

                    new fieldListMultiple('options:show_list', array(
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
                    )),

                    new fieldCheckbox('options:show_avatars', array(
                        'title' => LANG_WD_COMMENTS_LIST_SHOW_AVATARS,
                        'default' => true,
                    )),

                    new fieldCheckbox('options:show_text', array(
                        'title' => LANG_WD_COMMENTS_LIST_SHOW_TEXT,
                        'default' => false,
                    )),

                    new fieldNumber('options:limit', array(
                        'title' => LANG_LIST_LIMIT,
                        'default' => 10,
                        'rules' => array(
                            array('required')
                        )
                    ))

                )
            )

        );

    }

}
