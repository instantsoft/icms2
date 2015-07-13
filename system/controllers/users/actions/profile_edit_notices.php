<?php

class actionUsersProfileEditNotices extends cmsAction {

    public function run($profile){

        $user = cmsUser::getInstance();
        $template = cmsTemplate::getInstance();

        // проверяем наличие доступа
        if ($profile['id'] != $user->id && !$user->is_admin) { cmsCore::error404(); }

        $notify_types = cmsEventsManager::hookAll('user_notify_types');

        $form = new cmsForm();

        $fieldset_id = $form->addFieldset();

        $default_options = array('', 'email', 'pm', 'both');

        foreach($notify_types as $list){
            foreach($list as $name=>$type){

                $options = array();

                if(!isset($type['options'])) { $type['options'] = $default_options; }

                foreach($type['options'] as $option){
                    if (!$option){
                        $options[''] = LANG_USERS_NOTIFY_VIA_NONE;
                    } else {
                        $options[$option] = constant('LANG_USERS_NOTIFY_VIA_'.mb_strtoupper($option));
                    }
                }

                $form->addField($fieldset_id, new fieldList($name, array(
                    'title' => $type['title'],
                    'default' => 'email',
                    'items' => $options
                )));

            }
        }

        // Форма отправлена?
        $is_submitted = $this->request->has('submit');

        $options = $this->model->getUserNotifyOptions($profile['id']);

        if ($is_submitted){

            // Парсим форму и получаем поля записи
            $options = array_merge($options, $form->parse($this->request, $is_submitted, $options));

            // Проверям правильность заполнения
            $errors = $form->validate($this,  $options);

            if (!$errors){

                // Обновляем профиль и редиректим на его просмотр
                $this->model->updateUserNotifyOptions($profile['id'], $options);

                $this->redirectTo('users', $profile['id']);

            }

            if ($errors){
                cmsUser::addSessionMessage(LANG_FORM_ERRORS, 'error');
            }

        }

        return $template->render('profile_edit_notices', array(
            'id' => $profile['id'],
            'profile' => $profile,
            'options' => $options,
            'form' => $form,
            'errors' => isset($errors) ? $errors : false
        ));

    }

}
