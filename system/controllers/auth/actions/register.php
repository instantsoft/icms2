<?php
class actionAuthRegister extends cmsAction {

    public function run(){

        if (cmsUser::isLogged() && !cmsUser::isAdmin()) { $this->redirectToHome(); }

        $users_model = cmsCore::getModel('users');
        $form = $this->getForm('registration');

        //
        // Добавляем поле для кода приглашения,
        // если регистрация доступна только по приглашениям
        //
        if ($this->options['is_reg_invites']){

            $fieldset_id = $form->addFieldsetToBeginning(LANG_REG_INVITED_ONLY);

            $form->addField($fieldset_id, new fieldString('inv', array(
                'title' => LANG_REG_INVITE_CODE,
                'rules' => array(
                    array('required'),
                    array('min_length', 10),
                    array('max_length', 10),
                )
            )));

        }

        //
        // Добавляем поле выбора группы,
        // при наличии публичных групп
        //
        $public_groups = $users_model->getPublicGroups();

        if ($public_groups) {

            $pb_items = array();
            foreach($public_groups as $pb) { $pb_items[ $pb['id'] ] = $pb['title']; }

            $form->addFieldToBeginning('basic',
                new fieldList('group_id', array(
                        'title' => LANG_USER_GROUP,
                        'items' => $pb_items
                    )
                )
            );

        }

        //
        // Добавляем в форму обязательные поля профилей
        //
        $content_model = cmsCore::getModel('content');
        $content_model->setTablePrefix('');
        $content_model->orderBy('ordering');
        $fields = $content_model->getRequiredContentFields('users');

        // Разбиваем поля по группам
        $fieldsets = cmsForm::mapFieldsToFieldsets($fields);

        // Добавляем поля в форму
        foreach($fieldsets as $fieldset){

            $fieldset_id = $form->addFieldset($fieldset['title']);

            foreach($fieldset['fields'] as $field){
                if ($field['is_system']) { continue; }
                $form->addField($fieldset_id, $field['handler']);
            }

        }

        $user = array();

        if ($this->request->hasInQuery('inv')){
            $user['inv'] = $this->request->get('inv');
        }

        $is_submitted = $this->request->has('submit');

        if ($is_submitted){

            if (!$this->options['is_reg_enabled']){
                cmsCore::error404();
            }

            $errors = false;
            $is_captcha_valid = true;

            //
            // Проверяем капчу
            //
            if ($this->options['reg_captcha']){

                $is_captcha_valid = cmsEventsManager::hook('captcha_validate', $this->request);

                if (!$is_captcha_valid){
                    $errors = true;
                    cmsUser::addSessionMessage(LANG_CAPTCHA_ERROR, 'error');
                }

            }

            //
            // Парсим и валидируем форму
            //
            if (!$errors){

                $user = $form->parse($this->request, $is_submitted);

				$user['groups'] = array();

				if (!empty($this->options['def_groups'])){
					$user['groups'] = $this->options['def_groups'];
				}

                if (isset($user['group_id'])) {
					if (!in_array($user['group_id'], $user['groups'])){
						$user['groups'][] = $user['group_id'];
					}
				}

                //
                // убираем поля которые не относятся к выбранной пользователем группе
                //
                foreach($fieldsets as $fieldset){
                    foreach($fieldset['fields'] as $field){

                        if (!$field['groups_edit']) { continue; }
                        if (in_array(0, $field['groups_edit'])) { continue; }

                        if (!in_array($user['group_id'], $field['groups_edit'])){
                            $form->disableField($field['name']);
                            unset($user[$field['name']]);
                        }

                    }
                }

                $errors = $form->validate($this,  $user);

            }

            if (!$errors){

                //
                // проверяем код приглашения
                //
                if ($this->options['is_reg_invites']){
                    $invite = $this->model->getInviteByCode($user['inv']);
                    if (!$invite) {
                        $errors['inv'] = LANG_REG_WRONG_INVITE_CODE;
                    } else {
                        if ($this->options['is_invites_strict'] && ($invite['email'] != $user['email'])) {
                            $errors['inv'] = LANG_REG_WRONG_INVITE_CODE_EMAIL;
                        } else {
                            $user['inviter_id'] = $invite['user_id'];
                        }
                    }
                }

                //
                // проверяем допустимость e-mail, имени и IP
                //
                if (!$this->isEmailAllowed($user['email'])){
                    $errors['email'] = sprintf(LANG_AUTH_RESTRICTED_EMAIL, $user['email']);
                }

                if (!$this->isNameAllowed($user['nickname'])){
                    $errors['nickname'] = sprintf(LANG_AUTH_RESTRICTED_NAME, $user['nickname']);
                }

                if (!$this->isIPAllowed(cmsUser::get('ip'))){
                    cmsUser::addSessionMessage(sprintf(LANG_AUTH_RESTRICTED_IP, cmsUser::get('ip')), 'error');
                    $errors = true;
                }

            }

            if (!$errors){

                unset($user['inv']);

                //
                // Блокируем пользователя, если включена верификация e-mail
                //
                if ($this->options['verify_email']){
                    $user = array_merge($user, array(
                        'is_locked' => true,
                        'lock_reason' => LANG_REG_CFG_VERIFY_LOCK_REASON,
                        'pass_token' => string_random(32, $user['email']),
                        'date_token' => ''
                    ));
                }

                $result = $users_model->addUser($user);

                if ($result['success']){

					$user['id'] = $result['id'];

                    cmsUser::addSessionMessage(LANG_REG_SUCCESS, 'success');

                    // отправляем письмо верификации e-mail
                    if ($this->options['verify_email']){

                        $messenger = cmsCore::getController('messages');
                        $to = array('email' => $user['email'], 'name' => $user['nickname']);
                        $letter = array('name' => 'reg_verify');

                        $messenger->sendEmail($to, $letter, array(
                            'nickname' => $user['nickname'],
                            'page_url' => href_to_abs('auth', 'verify', $user['pass_token']),
                            'valid_until' => html_date(date('d.m.Y H:i', time() + ($this->options['verify_exp'] * 3600)), true),
                        ));

                        cmsUser::addSessionMessage(sprintf(LANG_REG_SUCCESS_NEED_VERIFY, $user['email']), 'info');

                    } else {

						cmsEventsManager::hook('user_registered', $user);

					}

                    $back_url = cmsUser::sessionGet('auth_back_url') ?
                                cmsUser::sessionGet('auth_back_url', true) :
                                false;

                    if ($back_url){
                        $this->redirect($back_url);
                    } else {
                        $this->redirectToHome();
                    }

                } else {
                    $errors = $result['errors'];
                }

            }

            if ($errors && $is_captcha_valid){
                cmsUser::addSessionMessage(LANG_FORM_ERRORS, 'error');
            }

        }

        // Капча
        if ($this->options['reg_captcha']){
            $captcha_html = cmsEventsManager::hook('captcha_html');
        }

        return cmsTemplate::getInstance()->render('registration', array(
            'user' => $user,
            'form' => $form,
            'captcha_html'=> isset($captcha_html) ? $captcha_html : false,
            'errors' => isset($errors) ? $errors : false
        ));

    }

}
