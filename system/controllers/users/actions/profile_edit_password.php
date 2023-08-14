<?php

class actionUsersProfileEditPassword extends cmsAction {

    public $lock_explicit_call = true;
    protected $extended_langs = ['auth'];
    private $verify_exp = 24;

    public function run($profile) {

        // Владельцы и админы могут редактировать
        if (!$this->is_own_profile && !$this->cms_user->is_admin) {
            return cmsCore::error404();
        }

        // Администраторы тут могут редактировать только свои профили
        if ($this->cms_user->is_admin && !$this->is_own_profile && $profile['is_admin']) {
            return cmsCore::error404();
        }

        $form = $this->getForm('password', [$profile]);

        $ups_key = 'users.change_email_' . md5($profile['email']);

        // Если разрешено, добавляем возможность смены email
        if (cmsUser::isAllowed('users', 'change_email', true, true)) {

            // срок подтверждения истёк
            $verify_hours_exp = null;

            $show_email_field = true;

            $sended = cmsUser::getUPS($ups_key);

            // уже ранее меняли или мы в процессе
            if ($sended) {

                $diff_days = intval((time() - $sended['timestamp']) / 86400);

                $verify_hours_exp = round((time() - $sended['timestamp']) / 3600) >= $this->verify_exp;

                if (cmsUser::isPermittedLimitHigher('users', 'change_email_period', $diff_days, true)) {
                    $show_email_field = false;
                }
            }

            $form->addFieldsetAfter('basic', LANG_EMAIL, 'email');

            // Не отправляли ничего
            if (!$sended || $verify_hours_exp === true || !empty($sended['accepted'])) {

                if ($show_email_field) {

                    $form->addField('email', new fieldString('new_email', [
                        'title' => LANG_EMAIL_NEW,
                        'hint'  => LANG_EMAIL_NEW_HINT,
                        'type'  => 'email',
                        'rules' => [
                            ['email'],
                            [function ($controller, $data, $value)use ($profile) {

                                if (!$value) { return true; }

                                $exists = $controller->model->getItemByField('{users}', 'email', $value);

                                if ($exists) {
                                    return LANG_REG_EMAIL_EXISTS;
                                }

                                return true;
                            }]
                        ]
                    ]));
                }
            } else {
                $form->addField('email', new fieldString('new_email_confirm_hash', [
                    'title' => LANG_EMAIL_NEW_HASH,
                    'rules' => [
                        ['required']
                    ]
                ]));
            }
        }

        $data = [
            '2fa' => $profile['2fa'],
            'new_email_confirm_hash' => $this->request->get('new_email_confirm_hash', '')
        ];

        if ($this->request->has('submit')) {

            $data = $form->parse($this->request, true);

            $errors = $form->validate($this, $data);

            if (!$errors) {

                $success_text = [LANG_SUCCESS_MSG];

                list($profile, $data, $form) = cmsEventsManager::hook('users_before_edit_password', [$profile, $data, $form]);

                $profile = array_merge($profile, $data);

                // если запрашивали смену email
                if (!empty($data['new_email'])) {

                    $verify_data = [
                        'email'     => $data['new_email'],
                        'timestamp' => time(),
                        'hash'      => string_random()
                    ];

                    // На новый email
                    cmsUser::setUPS($ups_key, $verify_data);

                    // письмо на новый email
                    $this->controller_messages->sendEmail(['email' => $data['new_email'], 'name' => $profile['nickname']], ['name' => 'email_verify'], [
                        'nickname'    => $profile['nickname'],
                        'page_url'    => href_to_profile($profile, ['edit', 'password'], true) . '?new_email_confirm_hash=' . $verify_data['hash'],
                        'hash'        => $verify_data['hash'],
                        'valid_until' => html_date(date('d.m.Y H:i', time() + ($this->verify_exp * 3600)), true)
                    ]);

                    $success_text[] = sprintf(LANG_USERS_EMAIL_VERIFY, $data['new_email']);
                }

                // Пришло подтверждение
                if (!empty($data['new_email_confirm_hash'])) {

                    if ($sended['hash'] === $data['new_email_confirm_hash']) {

                        $verify_data_old = [
                            'accepted'  => 1,
                            'email'     => $profile['email'],
                            'timestamp' => time(),
                            'hash'      => string_random()
                        ];

                        // На старый email
                        cmsUser::setUPS('users.change_email_' . md5($sended['email']), $verify_data_old);

                        // уведомление на старый
                        $this->controller_messages->sendEmail(
                            ['email' => $profile['email'], 'name' => $profile['nickname']],
                            ['name' => 'email_verify_notice'],
                            ['nickname'  => $profile['nickname'], 'new_email' => $sended['email']]
                        );

                        $profile['email'] = $sended['email'];
                    } else {

                        $errors['new_email_confirm_hash'] = LANG_CONFIRM_CODE_ERROR;
                    }
                }

                if (!$errors) {

                    $result = $this->model->updateUser($profile['id'], $profile);

                    if ($result['success']) {

                        list($profile, $data, $form) = cmsEventsManager::hook('users_after_edit_password', [$profile, $data, $form]);

                        if (!empty($data['password1'])) {
                            $success_text[] = LANG_PASS_CHANGED;
                        }

                        foreach ($success_text as $stext) {
                            cmsUser::addSessionMessage($stext, 'success');
                        }

                        $this->redirectTo('users', $profile['id']);

                    } else {
                        $errors = $result['errors'];
                    }
                }
            }

            if ($errors) {
                cmsUser::addSessionMessage(LANG_FORM_ERRORS, 'error');
            }
        }

        return $this->cms_template->render('profile_edit_password', [
            'id'      => $profile['id'],
            'profile' => $profile,
            'data'    => $data,
            'form'    => $form,
            'errors'  => isset($errors) ? $errors : false
        ]);
    }

}
