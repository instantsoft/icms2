<?php

class actionUsersProfileEditPrivacy extends cmsAction {

    public $lock_explicit_call = true;

    public function run($profile){

        // проверяем наличие доступа
        if (!$this->is_own_profile && !$this->cms_user->is_admin) { cmsCore::error404(); }

        $pricacy_types = cmsEventsManager::hookAll('user_privacy_types');

        $form = new cmsForm();

        $fieldset_id = $form->addFieldset();

        $default_options = array('', 'anyone', 'friends');

        foreach($pricacy_types as $list){
            foreach($list as $name=>$type){

                $options = array();

                if(!isset($type['options'])) { $type['options'] = $default_options; }

                foreach($type['options'] as $option){
                    if (!$option){
                        $options[''] = LANG_USERS_PRIVACY_FOR_NOBODY;
                    } else {
                        $options[$option] = constant('LANG_USERS_PRIVACY_FOR_'.mb_strtoupper($option));
                    }
                }

                $form->addField($fieldset_id, new fieldList($name, array(
                    'title' => $type['title'],
                    'default' => 'anyone',
                    'items' => $options
                )));

            }
        }

        // Форма отправлена?
        $is_submitted = $this->request->has('submit');

        $options = $this->model->getUserPrivacyOptions($profile['id']);

        if ($is_submitted){

            // Парсим форму и получаем поля записи
            $options = array_merge($options, $form->parse($this->request, $is_submitted, $options));

            // Проверям правильность заполнения
            $errors = $form->validate($this,  $options);

            if (!$errors){

                // Обновляем профиль и редиректим на его просмотр
                $this->model->updateUserPrivacyOptions($profile['id'], $options);

                cmsUser::addSessionMessage(LANG_SUCCESS_MSG, 'success');

                $this->redirectTo('users', $profile['id']);

            }

            if ($errors){
                cmsUser::addSessionMessage(LANG_FORM_ERRORS, 'error');
            }

        }

        return $this->cms_template->render('profile_edit_privacy', array(
            'id'      => $profile['id'],
            'profile' => $profile,
            'options' => $options,
            'form'    => $form,
            'errors'  => isset($errors) ? $errors : false
        ));

    }

}
