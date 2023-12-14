<?php

class actionMessagesPmailing extends cmsAction {

    public function run($group_id = 0) {

        $form = $this->getForm('pmailing');

        $mailing = ['groups' => [$group_id]];

        if ($this->request->has('submit')) {

            $mailing = $form->parse($this->request, true);

            $errors = $form->validate($this, $mailing);

            if ($mailing['sender_user_email']) {

                $user = $this->model_users->getUserByEmail($mailing['sender_user_email']);
                if (!$user && !$errors) {
                    $errors['sender_user_email'] = ERR_USER_NOT_FOUND;
                }
            }

            if (!$errors) {

                $sender          = !empty($user['id']) ? $user : $this->cms_user;
                $sender_id       = is_object($sender) ? $sender->id : $sender['id'];
                $sender_nickname = is_object($sender) ? $sender->nickname : $sender['nickname'];

                if ($mailing['groups']) {
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

                    $mailing['message_text'] = cmsEventsManager::hook('html_filter', $mailing['message_text']);

                    if ($mailing['type'] === 'message') {

                        $messages_ids = $this->controller_messages->sendMessage($mailing['message_text']);

                        $count = is_array($messages_ids) ? count($messages_ids) : ($messages_ids ? 1 : 0);

                        if ($count) {

                            $this->controller_messages->clearRecipients();

                            foreach ($recipients as $user_id) {

                                if ($this->model->getNewMessagesCount($user_id) == 1) {
                                    $this->controller_messages->addRecipient($user_id);
                                }
                            }

                            $this->controller_messages->sendNoticeEmail('messages_new', [
                                'user_url'      => href_to_profile($sender, false, true),
                                'user_nickname' => $sender_nickname,
                                'message'       => strip_tags($mailing['message_text'])
                            ]);
                        }
                    }

                    if ($mailing['type'] === 'notify') {

                        $notices_ids = $this->controller_messages->sendNoticePM(array(
                            'content' => $mailing['message_text']
                        ));

                        $count = is_array($notices_ids) ? count($notices_ids) : ($notices_ids ? 1 : 0);
                    }

                    if ($mailing['type'] === 'email') {

                        $emails = $this->model_users->
                            filterIn('id', array_keys($recipients))->
                            limit(false)->selectOnly('i.email', 'email')->select('nickname')->
                            get('{users}', function ($user) {
                                return $user['nickname'];
                            }, 'email');

                        foreach ($emails as $email => $nickname) {
                            $this->controller_messages->sendEmail(['email' => $email], [
                                'text' => $mailing['message_text']
                            ], ['nickname' => $nickname]);
                        }

                        $count = count($emails);
                    }

                    cmsUser::addSessionMessage(sprintf(
                        LANG_PM_PMAILING_SENDED,
                        html_spellcount($count, string_lang('LANG_PM_' . $mailing['type']), false, false, 0)
                    ), ($count ? 'success' : 'info'));
                }

                if (!$recipients) {
                    cmsUser::addSessionMessage(LANG_PM_PMAILING_NOT_RECIPIENTS, 'info');
                }

                return $this->redirectToAction('pmailing', ($group_id ? $group_id : false));
            }

            if ($errors) {
                cmsUser::addSessionMessage(LANG_FORM_ERRORS, 'error');
            }
        }

        return $this->cms_template->render('backend/pmailing', [
            'mailing' => $mailing,
            'form'    => $form,
            'errors'  => isset($errors) ? $errors : false
        ]);
    }
}
