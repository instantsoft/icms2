<?php

class actionUsersProfileEdit extends cmsAction {

    public $lock_explicit_call = true;

    public function run($profile, $do=false, $param=false){

		if (!cmsUser::isLogged()) { cmsCore::error404(); }

        // если нужно, передаем управление другому экшену
        if ($do){
            $this->runAction('profile_edit_'.$do, array($profile) + array_slice($this->params, 2, null, true));
            return;
        }

        // проверяем наличие доступа
        if ($profile['id'] != $this->cms_user->id && !$this->cms_user->is_admin) { cmsCore::error404(); }

        // Получаем поля
        $content_model = cmsCore::getModel('content');
        $content_model->setTablePrefix('');
        $content_model->orderBy('ordering');
        $fields = $content_model->getContentFields('{users}', $profile['id']);

        // Строим форму
        $form = new cmsForm();

        // Разбиваем поля по группам
        $fieldsets = cmsForm::mapFieldsToFieldsets($fields, function($field, $user){

            // проверяем что группа пользователя имеет доступ к редактированию этого поля
            if ($field['groups_edit'] && !$user->isInGroups($field['groups_edit'])) { return false; }

            return true;

        });

        // Добавляем поля в форму
        foreach($fieldsets as $fieldset){

            $fieldset_id = $form->addFieldset($fieldset['title']);

            foreach($fieldset['fields'] as $field){

                // добавляем поле в форму
                $form->addField($fieldset_id, $field['handler']);

            }

        }

        // Добавляем поле выбора часового пояса
        $fieldset_id = $form->addFieldset( LANG_TIME_ZONE );
        $form->addField($fieldset_id, new fieldList('time_zone', array(
            'default' => $this->cms_config->time_zone,
            'generator' => function($item){
                return cmsCore::getTimeZones();
            }
        )));

        // Форма отправлена?
        $is_submitted = $this->request->has('submit');

        if ($is_submitted){

            // Парсим форму и получаем поля записи
            $new = $form->parse($this->request, $is_submitted, $profile);
            $old = $profile;
            $profile = array_merge($profile, $new);

            // Проверям правильность заполнения
            $errors = $form->validate($this,  $profile);

            if (!$errors){
                $is_allowed = cmsEventsManager::hookAll('user_profile_update', $profile, true);
                if (is_array($is_allowed)) {
                    $errors = array();
                    foreach ($is_allowed as $error_list) {
                        if(is_array($error_list) && $error_list){
                            $errors = array_merge($error_list);
                        }
                    }
                }
            }

            if (!$errors){

                // Обновляем профиль и редиректим на его просмотр
                $this->model->updateUser($profile['id'], $profile);

                // Отдельно обновляем часовой пояс в сессии
                cmsUser::sessionSet('user_data:time_zone', $profile['time_zone']);

                // Постим уведомление о смене аватара в ленту
                if (!$this->model->isAvatarsEqual($new['avatar'], $old['avatar'])){
                    $activity_controller = cmsCore::getController('activity');
                    $activity_controller->deleteEntry($this->name, 'avatar', $profile['id']);
					if (!empty($new['avatar'])){
						$activity_controller->addEntry($this->name, 'avatar', array(
							'user_id'       => $profile['id'],
                            'subject_title' => $profile['nickname'],
                            'subject_id'    => $profile['id'],
                            'subject_url'   => href_to_rel('users', $profile['id']),
                            'is_private'    => 0,
                            'group_id'      => null,
                            'images'        => array(
                                array(
                                    'url' => href_to_rel('users', $profile['id']),
                                    'src' => html_image_src($new['avatar'], 'normal')
                                )
                            ),
                            'images_count'  => 1
                        ));
					}
                }

                cmsUser::addSessionMessage(LANG_SUCCESS_MSG, 'success');

                $this->redirectTo('users', $profile['id']);

            }

            if ($errors){
                cmsUser::addSessionMessage(LANG_FORM_ERRORS, 'error');
            }

        }

        return $this->cms_template->render('profile_edit', array(
            'do'      => 'edit',
            'id'      => $profile['id'],
            'profile' => $profile,
            'form'    => $form,
            'errors'  => isset($errors) ? $errors : false
        ));

    }

}
