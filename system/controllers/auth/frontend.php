<?php
/**
 * @property \modelMessages $model_messages
 * @property \modelContent $model_content
 * @property \modelUsers $model_users
 */
class auth extends cmsFrontend {

    protected $useOptions = true;
    public $useSeoOptions = true;

    public function getOptions() {

        $options = parent::getOptions();

        if (!empty($options['2fa'])) {

            foreach ($options['2fa'] as $o2fa) {

                $params = explode(':', $o2fa);

                $options['2fa_params'][$o2fa] = [
                    'controller' => $params[0],
                    'action'     => !empty($params[1]) ? $params[1] : 'login_2fa'
                ];
            }
        }

        return $options;
    }

    public function actionIndex() {
        $this->executeAction('login');
    }

    public function actionLogout() {

        if (!cmsForm::validateCSRFToken($this->request->get('csrf_token', ''))) {
            return $this->redirectToHome();
        }

        cmsEventsManager::hook('auth_logout', $this->cms_user->id);

        cmsUser::logout();

        if (!function_exists('get_headers')) {
            return $this->redirectToHome();
        }

        $back_url = $this->getBackURL();

        if ($back_url !== $this->cms_config->root) {

            $parsed = parse_url($back_url);

            if (!$parsed || empty($parsed['scheme'])) {
                return $this->redirectToHome();
            }

            $h = get_headers($back_url, true);

            if (!$h || empty($h[0])) {
                return $this->redirectToHome();
            }

            $code = intval(substr($h[0], 9, 3));

            if ($code < 400) {
                return $this->redirect($back_url);
            }
        }

        return $this->redirectToHome();
    }

//============================================================================//
//============================================================================//

    public function isEmailAllowed($value) {

        $list = $this->options['restricted_emails'];

        return !string_in_mask_list($value, $list);
    }

    public function isNameAllowed($value) {

        $list = $this->options['restricted_names'];

        return !string_in_mask_list($value, $list);
    }

    public function isIPAllowed($value) {

        $list = $this->options['restricted_ips'];

        return !string_in_mask_list($value, $list);
    }

//============================================================================//
//============================================================================//

    public function sendGreetMsg($user) {

        if(empty($this->options['send_greetmsg'])){
            return;
        }

        if(empty($this->options['greetmsg'])){
            return;
        }

        $this->model_messages->addNotice([$user['id']], [
            'content' => $this->options['greetmsg']
        ]);

        return;
    }

    public function getAuthRedirectUrl($value) {

        $url = href_to_home();

        $user = cmsUser::sessionGet('user');
        if (empty($user['id'])) {
            return $url;
        }

        $back_url = $this->getBackURL();
        if (strpos($back_url, href_to('auth', 'login')) !== false) {
            $back_url = $url;
        }
        switch ($value) {
            case 'none': $url = $back_url;
                break;
            case 'index': $url = href_to_home();
                break;
            case 'profile': $url = href_to_profile($user);
                break;
            case 'profileedit': $url = href_to_profile($user, ['edit']);
                break;
        }

        return $url;
    }

    public function getRegistrationForm($has_inv_code = false) {

        $form = $this->getForm('registration');

        //
        // Добавляем поле для кода приглашения,
        // если регистрация доступна только по приглашениям
        //
        if ($this->options['is_reg_invites'] || $has_inv_code) {

            $fieldset_id = $form->addFieldsetToBeginning(!$this->options['is_reg_invites'] ? '' : LANG_REG_INVITED_ONLY);

            $form->addField($fieldset_id, new fieldString('inv', array(
                'title'      => LANG_REG_INVITE_CODE,
                'attributes' => [
                    'readonly' => !$this->options['is_reg_invites'] ? true : false
                ],
                'rules' => [
                    ['required'],
                    ['min_length', 10],
                    ['max_length', 10]
                ]
            )));
        }

        //
        // Добавляем поле выбора группы,
        // при наличии публичных групп
        //
        $public_groups = $this->model_users->getPublicGroups();

        if ($public_groups) {

            $pb_items = array();
            foreach ($public_groups as $pb) {
                $pb_items[$pb['id']] = $pb['title'];
            }

            $form->addFieldToBeginning('basic',
                new fieldList('group_id', array(
                    'title' => LANG_USER_GROUP,
                    'items' => $pb_items
                ))
            );
        }

        //
        // Добавляем в форму обязательные поля профилей
        //
        $fields = $this->model_content->setTablePrefix('')->orderBy('ordering')->getRequiredContentFields('{users}');

        // Разбиваем поля по группам
        $fieldsets = cmsForm::mapFieldsToFieldsets($fields);

        // Добавляем поля в форму
        foreach ($fieldsets as $fieldset) {

            $fieldset_id = $form->addFieldset($fieldset['title']);

            foreach ($fieldset['fields'] as $field) {
                $form->addField($fieldset_id, $field['handler']);
            }
        }

        // Капча
        if ($this->options['reg_captcha']) {

            $fieldset_id = $form->addFieldset(LANG_CAPTCHA_CODE, 'regcaptcha');

            $form->addField($fieldset_id,
                    new fieldCaptcha('capcha')
            );
        }

        return cmsEventsManager::hook('form_auth_registration_full', [$form, $fieldsets]);
    }

}
