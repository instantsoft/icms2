<?php

class actionUsersProfileInvites extends cmsAction {

    public $lock_explicit_call = true;

    public function run($profile) {

        // проверяем наличие доступа
        if (!$this->is_own_profile) {
            return cmsCore::error404();
        }

        if (!$profile['invites_count']) {
            return $this->redirectToAction($profile['id']);
        }

        // Форма отправлена?
        $is_submitted = $this->request->has('submit');

        $form = new cmsForm();

        $fieldset_id = $form->addFieldset(sprintf(LANG_USERS_INVITES_COUNT, html_spellcount($profile['invites_count'], LANG_USERS_INVITES_SPELLCOUNT)));

        if ($profile['invites_count'] > 1) {

            $form->addField($fieldset_id, new fieldText('emails', [
                'title'         => LANG_USERS_INVITES_EMAILS,
                'hint'          => LANG_USERS_INVITES_EMAILS_HINT,
                'is_strip_tags' => true,
                'rules'         => [
                    ['required']
                ]
            ]));
        }

        if ($profile['invites_count'] == 1) {

            $form->addField($fieldset_id, new fieldString('emails', [
                'title' => LANG_USERS_INVITES_EMAIL,
                'rules' => [
                    ['required'],
                    ['email']
                ]
            ]));
        }

        $fieldset_id = $form->addFieldset(LANG_USERS_INVITES_LINKS);

        $invites = $this->model_auth->getUserInvites($this->cms_user->id);

        foreach ($invites as $invite) {

            $form->addField($fieldset_id, new fieldString('invite:' . $invite['id'], [
                'default'    => $invite['page_url'],
                'attributes' => [
                    'readonly' => '',
                    'class'    => 'icms-click-select'
                ]
            ]));
        }

        $input = [];

        if ($is_submitted) {

            // Парсим форму и получаем поля записи
            $input = $form->parse($this->request, $is_submitted);

            // Проверям правильность заполнения
            $errors = $form->validate($this, $input);

            if (!$errors) {

                $results = $this->sendInvites($profile, $input['emails']);

                return $this->cms_template->render('profile_invites_results', [
                    'id'      => $profile['id'],
                    'profile' => $profile,
                    'results' => $results
                ]);
            }

            if ($errors) {
                cmsUser::addSessionMessage(LANG_FORM_ERRORS, 'error');
            }
        }

        return $this->cms_template->render('profile_invites', [
            'id'      => $profile['id'],
            'profile' => $profile,
            'invites' => $invites,
            'form'    => $form,
            'input'   => $input,
            'errors'  => $errors ?? false
        ]);
    }

    private function sendInvites($profile, $emails_list) {

        $results = [
            'success' => [],
            'failed'  => []
        ];

        $emails = string_explode_list($emails_list);

        foreach ($emails as $email) {

            if ($this->validate_email($email) !== true) {
                $results['failed'][$email] = ERR_VALIDATE_EMAIL;
                continue;
            }

            if ($this->model->getUserByEmail($email)) {
                $results['failed'][$email] = LANG_REG_EMAIL_EXISTS;
                continue;
            }

            if (!$this->controller_auth->isEmailAllowed($email)) {
                $results['failed'][$email] = LANG_AUTH_RESTRICTED_EMAILS;
                continue;
            }

            $invite = $this->model_auth->getNextInvite($this->cms_user->id);

            $to     = ['email' => $email, 'name' => $email];
            $letter = ['name' => 'users_invite'];

            $this->controller_messages->sendEmail($to, $letter, [
                'nickname' => $this->cms_user->nickname,
                'code'     => $invite['code'],
                'page_url' => href_to_abs('auth', 'register') . "?inv={$invite['code']}"
            ]);

            $results['success'][$email] = true;

            $this->model_auth->markInviteSended($invite['id'], $this->cms_user->id, $email);

            if ((count($results['success']) + count($results['failed'])) >= $profile['invites_count']) {
                break;
            }
        }

        return $results;
    }

}
