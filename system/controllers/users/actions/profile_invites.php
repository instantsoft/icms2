<?php

class actionUsersProfileInvites extends cmsAction {

    public $lock_explicit_call = true;

    public function run($profile){

        // проверяем наличие доступа
        if (!$this->is_own_profile) { cmsCore::error404(); }

        // Форма отправлена?
        $is_submitted = $this->request->has('submit');

        if (!$is_submitted && !$profile['invites_count']) { cmsCore::error404(); }

        $form = new cmsForm();

        $fieldset_id = $form->addFieldset();

        if ($profile['invites_count'] > 1){

            $form->addField($fieldset_id, new fieldText('emails', array(
                'title' => LANG_USERS_INVITES_EMAILS,
                'hint' => LANG_USERS_INVITES_EMAILS_HINT,
                'rules' => array(
                    array('required')
                )
            )));

        }

        if ($profile['invites_count'] == 1){

            $form->addField($fieldset_id, new fieldString('emails', array(
                'title' => LANG_USERS_INVITES_EMAIL,
                'rules' => array(
                    array('required'),
                    array('email')
                )
            )));

        }

        $input = array();

        if ($is_submitted){

            // Парсим форму и получаем поля записи
            $input = $form->parse($this->request, $is_submitted);

            // Проверям правильность заполнения
            $errors = $form->validate($this,  $input);

            if (!$errors){

                $results = $this->sendInvites($profile, $input['emails']);

                return $this->cms_template->render('profile_invites_results', array(
                    'id' => $profile['id'],
                    'profile' => $profile,
                    'results' => $results,
                ));

            }

            if ($errors){
                cmsUser::addSessionMessage(LANG_FORM_ERRORS, 'error');
            }

        }

        return $this->cms_template->render('profile_invites', array(
            'id'      => $profile['id'],
            'profile' => $profile,
            'form'    => $form,
            'input'   => $input,
            'errors'  => isset($errors) ? $errors : false
        ));

    }

    private function sendInvites($profile, $emails_list){

        $results = array(
            'success' => array(),
            'failed' => array()
        );

        $emails = string_explode_list($emails_list);

        $auth_controller = cmsCore::getController('auth');

        $auth_model = cmsCore::getModel('auth');
        $messenger = cmsCore::getController('messages');

        foreach($emails as $email){

            if ($this->validate_email($email) !== true){
                $results['failed'][$email] = ERR_VALIDATE_EMAIL;
                continue;
            }

            if ($this->model->getUserByEmail($email)){
                $results['failed'][$email] = LANG_REG_EMAIL_EXISTS;
                continue;
            }

            if (!$auth_controller->isEmailAllowed($email)){
                $results['failed'][$email] = LANG_AUTH_RESTRICTED_EMAILS;
                continue;
            }

            $invite = $auth_model->getNextInvite($this->cms_user->id);

            $to = array('email' => $email, 'name' => $email);
            $letter = array('name' => 'users_invite');

            $messenger->sendEmail($to, $letter, array(
                'nickname' => $this->cms_user->nickname,
                'code' => $invite['code'],
                'page_url' => href_to_abs('auth', 'register') . "?inv={$invite['code']}",
            ));

            $results['success'][$email] = true;

            $auth_model->markInviteSended($invite['id'], $this->cms_user->id, $email);

            if ((sizeof($results['success']) + sizeof($results['failed'])) >= $profile['invites_count']) { break; }

        }

        return $results;

    }

}
