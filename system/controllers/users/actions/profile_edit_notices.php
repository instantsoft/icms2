<?php

class actionUsersProfileEditNotices extends cmsAction {

    public $lock_explicit_call = true;

    public function run($profile){

        // проверяем наличие доступа
        if (!$this->is_own_profile && !$this->cms_user->is_admin) { cmsCore::error404(); }

        $notify_types = $this->model->getUserNotifyTypes();

        $form = new cmsForm();

        $fieldset_id = $form->addFieldset();

        foreach($notify_types as $name => $field_options){

            $form->addField($fieldset_id, new fieldList($name, $field_options));

        }

        $options = $this->model->getUserNotifyOptions($profile['id']);

        list($form, $profile, $options) = cmsEventsManager::hook('users_profile_notices_form', [$form, $profile, $options]);

        if ($this->request->has('submit')){

            // Парсим форму и получаем поля записи
            $options = array_merge($options, $form->parse($this->request, true, $options));

            // Проверям правильность заполнения
            $errors = $form->validate($this,  $options);

            if (!$errors){

                // Обновляем профиль и редиректим на его просмотр
                $this->model->updateUserNotifyOptions($profile['id'], $options);

                cmsUser::addSessionMessage(LANG_SUCCESS_MSG, 'success');

                $this->redirectTo('users', $profile['id']);

            }

            if ($errors){
                cmsUser::addSessionMessage(LANG_FORM_ERRORS, 'error');
            }

        }

        return $this->cms_template->render('profile_edit_notices', array(
            'id'      => $profile['id'],
            'profile' => $profile,
            'options' => $options,
            'form'    => $form,
            'errors'  => isset($errors) ? $errors : false
        ));

    }

}
