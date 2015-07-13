<?php

class onCommentsUserNotifyTypes extends cmsAction {

    public function run(){

        return array(
            'comments_new' => array(
                'title'=>LANG_COMMENTS_NOTIFY_NEW
            ),
            'comments_reply' => array(
                'title'=>LANG_COMMENTS_NOTIFY_REPLY,
                'options' => array('', 'email')
            )
        );

    }

}
