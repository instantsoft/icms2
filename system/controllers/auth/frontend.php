<?php
class auth extends cmsFrontend {

    protected $useOptions = true;
    public $useSeoOptions = true;

    public function getOptions(){

        $options = parent::getOptions();

        if(!empty($options['2fa'])){

            foreach ($options['2fa'] as $o2fa) {

                $params = explode(':', $o2fa);

                $options['2fa_params'][$o2fa] = [
                    'controller' => $params[0],
                    'action' => !empty($params[1]) ? $params[1] : 'login_2fa'
                ];

            }

        }

        return $options;

    }

	public function actionIndex(){
        $this->executeAction('login');
  	}

    public function actionLogout(){

        cmsEventsManager::hook('auth_logout', $this->cms_user->id);

        cmsUser::logout();

        if(!function_exists('get_headers')){
            $this->redirectToHome();
        }

        $back_url = $this->getBackURL();

        if($back_url != $this->cms_config->root){

            $h = get_headers($this->getBackURL(), true);
            $code = substr($h[0], 9, 3);

            if((int)$code < 400){
                $this->redirect($back_url);
            }

        }

        $this->redirectToHome();

    }

//============================================================================//
//============================================================================//

    public function isEmailAllowed($value){

        $list = $this->options['restricted_emails'];

        return !string_in_mask_list($value, $list);

    }

    public function isNameAllowed($value){

        $list = $this->options['restricted_names'];

        return !string_in_mask_list($value, $list);

    }

    public function isIPAllowed($value){

        $list = $this->options['restricted_ips'];

        return !string_in_mask_list($value, $list);

    }

//============================================================================//
//============================================================================//

    public function getAuthRedirectUrl($value){

        $url = href_to_home();

		$user_id = cmsUser::sessionGet('user:id');
		if (!$user_id){ return $url; }

        $back_url = $this->getBackURL();
        if(strpos($back_url, href_to('auth', 'login')) !== false) {
            $back_url = $url;
        }
		switch($value){
			case 'none':        $url = $back_url; break;
			case 'index':       $url = href_to_home(); break;
			case 'profile':     $url = href_to('users', $user_id); break;
			case 'profileedit': $url = href_to('users', $user_id, 'edit'); break;
		}

		return $url;

    }

    public function getRegistrationForm() {

        $form = $this->getForm('registration');

        //
        // Добавляем поле для кода приглашения,
        // если регистрация доступна только по приглашениям
        //
        if ($this->options['is_reg_invites'] || $this->request->has('inv')){

            $fieldset_id = $form->addFieldsetToBeginning(!$this->options['is_reg_invites'] ? '' : LANG_REG_INVITED_ONLY);

            $form->addField($fieldset_id, new fieldString('inv', array(
                'title' => LANG_REG_INVITE_CODE,
                'attributes' => array(
                    'readonly' => !$this->options['is_reg_invites'] ? true : false
                ),
                'rules' => array(
                    array('required'),
                    array('min_length', 10),
                    array('max_length', 10)
                )
            )));

        }

        //
        // Добавляем поле выбора группы,
        // при наличии публичных групп
        //
        $public_groups = $this->model_users->getPublicGroups();

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
        $fields = $this->model_content->setTablePrefix('')->orderBy('ordering')->getRequiredContentFields('{users}');

        // Разбиваем поля по группам
        $fieldsets = cmsForm::mapFieldsToFieldsets($fields);

        // Добавляем поля в форму
        foreach($fieldsets as $fieldset){

            $fieldset_id = $form->addFieldset($fieldset['title']);

            foreach($fieldset['fields'] as $field){

                if ($field['name'] == 'nickname') {
                    $form->addFieldToBeginning('basic', $field['handler']); continue;
                }

                $form->addField($fieldset_id, $field['handler']);

            }

        }

        // Капча
        if ($this->options['reg_captcha']){

            $fieldset_id = $form->addFieldset(LANG_CAPTCHA_CODE, 'regcaptcha');

            $form->addField($fieldset_id,
                new fieldCaptcha('capcha')
            );

        }

        return array($form, $fieldsets);

    }

}
