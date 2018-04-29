<?php

class actionAuthSendInvites extends cmsAction {

    public function run($group_id = 0, $user_id = 0) {

        cmsCore::loadControllerLanguage('users');

        $form = $this->getForm('send_invites');

        $data = array('groups' => array($group_id));

        if($user_id){

            $user = $this->model_users->getUser($user_id);

            if($user){
                $data['user_email'] = $user['email'];
            }

        }

        if ($this->request->has('submit') || $this->request->has('revoke_invites')) {

            $data = $form->parse($this->request, true);

            $errors = $form->validate($this, $data);

            if($data['user_email']){

                $user = $this->model_users->getUserByEmail($data['user_email']);
                if(!$user && !$errors){
                    $errors['user_email'] = ERR_USER_NOT_FOUND;
                }

            }

            if (!$errors) {

                // отправка пользователю, фильтрацию не учитываем
                if($data['user_email']){

                    $recipients = array($user['id']);

                // отправка группе
                } else {

                    if ($data['groups'] && $data['groups'] != array(0)) {
                        $this->model_users->filterGroups($data['groups']);
                    }

                    $this->model_users->filterGtEqual('karma', $data['invites_min_karma']);
                    $this->model_users->filterGtEqual('rating', $data['invites_min_rating']);

                    if($data['invites_min_days']){
                        $this->model_users->filterDateOlder('date_reg', $data['invites_min_days']);
                    }

                    $recipients = $this->model_users->
                            filterIsNull('is_locked')->
                            filterIsNull('is_deleted')->
                            limit(false)->getUsersIds();

                }

                if ($recipients) {

                    foreach($recipients as $recipient_id){

                        if ($this->request->has('submit')) {

                            $this->model->addInvites($recipient_id, $data['invites_qty']);

                            $this->model_messages->addNotice(array($recipient_id), array(
                                'content' => sprintf(LANG_AUTH_INVITE_SEND_COUNT, html_spellcount($data['invites_qty'], LANG_USERS_INVITES_SPELLCOUNT))
                            ));

                        } else {
                            $this->model->revokeInvites($recipient_id, $data['invites_qty']);
                        }

                    }

                    if ($this->request->has('submit')) {
                        cmsUser::addSessionMessage(LANG_AUTH_INVITE_SENDED, 'success');
                    } else {
                        cmsUser::addSessionMessage(LANG_AUTH_INVITE_REVOKED, 'success');
                    }

                    $this->redirectToAction('send_invites', ($group_id ? $group_id : false));

                }

                if (!$recipients) {
                    cmsUser::addSessionMessage(LANG_CP_USER_SEARCH_NOT_FOUND, 'info');
                }

            }

            if ($errors) {
                cmsUser::addSessionMessage(LANG_FORM_ERRORS, 'error');
            }
        }

        return $this->cms_template->render('backend/send_invites', array(
            'data'   => $data,
            'form'   => $form,
            'errors' => isset($errors) ? $errors : false
        ));
    }

}
