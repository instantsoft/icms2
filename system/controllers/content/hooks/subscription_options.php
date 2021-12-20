<?php

class onContentSubscriptionOptions extends cmsAction {

    public $disallow_event_db_register = true;

    public function run($subject){

        $ctype = $this->model->getContentTypeByName($subject);
        if(!$ctype){
            return [];
        }

        return [
            'letter_tpl'  => !empty($ctype['options']['subscriptions_letter_tpl']) ? $ctype['options']['subscriptions_letter_tpl'] : '',
            'notify_text' => !empty($ctype['options']['subscriptions_notify_text']) ? $ctype['options']['subscriptions_notify_text'] : ''
        ];
    }

}
