<?php

class actionMessagesPmailing extends cmsAction {

    public function run($group_id = 0) {

        $form = $this->getForm('pmailing');

        $mailing = array('groups' => array($group_id));

        if ($this->request->has('submit')) {

            $mailing = $form->parse($this->request, true);

            $mailing['message_text'] = cmsEventsManager::hook('html_filter', $mailing['message_text']);

            $errors = $form->validate($this, $mailing);

            if($mailing['sender_user_email']){

                $user = $this->model_users->getUserByEmail($mailing['sender_user_email']);
                if(!$user && !$errors){
                    $errors['sender_user_email'] = ERR_USER_NOT_FOUND;
                }

            }

            if (!$errors) {

                $sender_id = !empty($user['id']) ? $user['id'] : $this->cms_user->id;
                $sender_nickname = !empty($user['id']) ? $user['nickname'] : $this->cms_user->nickname;

                if ($mailing['groups'] && $mailing['groups'] != array(0)) {
                    $this->model_users->filterGroups($mailing['groups']);
                }

                $recipients = $this->model_users->
                        filterIsNull('is_locked')->
                        filterIsNull('is_deleted')->
                        limit(false)->getUsersIds();

                if ($recipients) {
                    if (isset($recipients[$sender_id])) {
                        unset($recipients[$sender_id]);
                    }
                }

                if ($recipients) {

                    $this->controller_messages->addRecipients(array_keys($recipients))->setSender($sender_id);

                    if ($mailing['type'] === 'message') {

                        $messages_ids = $this->controller_messages->sendMessage($mailing['message_text']);

                        $count = is_array($messages_ids) ? count($messages_ids) : ($messages_ids ? 1 : 0);

                        if($count){

                            $this->controller_messages->clearRecipients();

                            foreach ($recipients as $user_id) {

                                if($this->model->getNewMessagesCount($user_id) == 1){
                                    $this->controller_messages->addRecipient($user_id);
                                }

                            }

                            $this->controller_messages->sendNoticeEmail('messages_new', array(
                                'user_url'      => href_to_abs('users', $sender_id),
                                'user_nickname' => $sender_nickname,
                                'message'       => strip_tags($mailing['message_text'])
                            ));

                        }

                    }

                    if ($mailing['type'] === 'notify') {

                        $notices_ids = $this->controller_messages->sendNoticePM(array(
                            'content' => $mailing['message_text']
                        ));

                        $count = is_array($notices_ids) ? count($notices_ids) : ($notices_ids ? 1 : 0);

                    }

                    cmsUser::addSessionMessage(sprintf(
                        LANG_PM_PMAILING_SENDED,
                        html_spellcount($count, string_lang('LANG_PM_'.$mailing['type']), false, false, 0)
                    ), ($count ? 'success' : 'info'));

                }

                if (!$recipients) {
                    cmsUser::addSessionMessage(LANG_PM_PMAILING_NOT_RECIPIENTS, 'info');
                }

                $this->redirectToAction('pmailing', ($group_id ? $group_id : false));

            }

            if ($errors) {
                cmsUser::addSessionMessage(LANG_FORM_ERRORS, 'error');
            }
        }

        return $this->cms_template->render('backend/pmailing', array(
            'mailing' => $mailing,
            'form'    => $form,
            'errors'  => isset($errors) ? $errors : false
        ));
    }

}
