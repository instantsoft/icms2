<?php

class onMessagesQueueSendEmail extends cmsAction {

    public $disallow_event_db_register = true;

    public function run($attempt, $to, $letter, $is_nl2br_text = null) {

        $result = $this->sendEmailRaw($to, $letter, $is_nl2br_text);

        if ($result !== true && $attempt < cmsQueue::getMaxAttempts()) {
            return false;
        }

        return $result === true ? true : $result;
    }

}
