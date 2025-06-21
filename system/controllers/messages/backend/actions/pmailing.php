<?php
/**
 * @property \modelUsers $model_users
 * @property \messages $controller_messages
 */
class actionMessagesPmailing extends cmsAction {

    public function run($group_id = 0) {

        $history_key = 'admin_pmailing';

        $mailing = array_merge((cmsUser::getUPS($history_key) ?: []), ['groups' => [$group_id]]);

        $form = $this->getForm('pmailing');

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

                if ($mailing['filters']) {
                    $this->model_users->applyDatasetFilters(['filters' => $mailing['filters']]);
                }

                $recipients = $this->model_users->
                                filterIsNull('is_locked')->
                                filterIsNull('is_deleted')->
                                limit(false)->getUsersIds();

                if ($recipients) {
                    if (isset($recipients[$sender_id])) {
                        //unset($recipients[$sender_id]);
                    }
                }

                if ($recipients) {

                    $this->controller_messages->addRecipients(array_keys($recipients))->setSender($sender_id);

                    $message_text = cmsEventsManager::hook('html_filter', [
                        'text'         => $mailing['message_text'],
                        'typograph_id' => $mailing['typograph_id'],
                        'is_auto_br'   => $mailing['is_br'] ? true : null
                    ]);

                    if ($mailing['type'] === 'message') {

                        $messages_ids = $this->controller_messages->sendMessage($message_text);

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
                                'message'       => strip_tags($message_text)
                            ]);
                        }
                    }

                    if ($mailing['type'] === 'notify') {

                        $notices_ids = $this->controller_messages->sendNoticePM(array(
                            'content' => $message_text
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
                                'text' => $message_text
                            ], ['nickname' => $nickname]);
                        }

                        $count = count($emails);
                    }

                    cmsUser::addSessionMessage(sprintf(
                        LANG_PM_PMAILING_SENDED,
                        html_spellcount($count, string_lang('LANG_PM_' . $mailing['type']), null, null, 0)
                    ), ($count ? 'success' : 'info'));

                    cmsUser::setUPS($history_key, $mailing);
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
            'errors'  => $errors ?? false
        ]);
    }
}
