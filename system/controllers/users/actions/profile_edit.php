<?php

class actionUsersProfileEdit extends cmsAction {

    public $lock_explicit_call = true;

    public function run($profile, $do = false, $param = false) {

        if (!$this->cms_user->is_logged) { cmsCore::error404(); }

        // если нужно, передаем управление другому экшену
        if ($do){
            $this->runExternalAction('profile_edit_'.$do, array($profile) + array_slice($this->params, 2, null, true));
            return;
        }

        $back_url = $this->request->get('back', '');

        // проверяем наличие доступа
        if (!$this->is_own_profile && !$this->cms_user->is_admin) { cmsCore::error404(); }

        // Получаем поля
        $content_model = cmsCore::getModel('content');
        $content_model->setTablePrefix('');
        $content_model->orderBy('ordering');
        $fields = $content_model->getContentFields('{users}', $profile['id']);

        // Строим форму
        $form = new cmsForm();

        // Разбиваем поля по группам
        $fieldsets = cmsForm::mapFieldsToFieldsets($fields, function($field, $user) use ($profile){

            // проверяем что группа пользователя имеет доступ к редактированию этого поля
            if ($field['groups_edit'] && !$user->isInGroups($field['groups_edit'])) {
                // если группа пользователя не имеет доступ к редактированию этого поля,
                // проверяем на доступ к нему для авторов
                if (!empty($profile['id']) && !empty($field['options']['author_access'])){
                    if (!in_array('is_edit', $field['options']['author_access'])){ return false; }
                    if ($profile['id'] == $user->id){ return true; }
                }
                return false;
            }
            return true;

        });

        // Добавляем поля в форму
        foreach($fieldsets as $fieldset){

            $fid = $fieldset['title'] ? md5($fieldset['title']) : null;

            $fieldset_id = $form->addFieldset($fieldset['title'], $fid);

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

                list($profile, $old) = cmsEventsManager::hook('users_before_update', [$profile, $old]);

                // Обновляем профиль и редиректим на его просмотр
                $this->model->updateUser($profile['id'], $profile);

                list($profile, $old) = cmsEventsManager::hook('users_after_update', [$profile, $old]);

                // Отдельно обновляем часовой пояс в сессии
                cmsUser::sessionSet('user:time_zone', $profile['time_zone']);

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
                                    'src' => html_image_src($new['avatar'], $fields['avatar']['options']['size_full'])
                                )
                            ),
                            'images_count'  => 1
                        ));
					}
                }

                $content = cmsCore::getController('content', $this->request);

                $parents = $content->model->getContentTypeParents(null, $this->name);

                if($parents){
                    $content->bindItemToParents(array('id' => null, 'name' => $this->name, 'controller' => $this->name), $profile, $parents);
                }

                cmsUser::addSessionMessage(LANG_SUCCESS_MSG, 'success');

                if ($back_url){
                    $this->redirect($back_url);
                } else {
                    $this->redirectTo('users', $profile['id']);
                }

            }

            if ($errors){
                cmsUser::addSessionMessage(LANG_FORM_ERRORS, 'error');
            }

        }

        $allow_delete_profile = (cmsUser::isAllowed('users', 'delete', 'any') ||
            (cmsUser::isAllowed('users', 'delete', 'my') && $this->is_own_profile));

        return $this->cms_template->render('profile_edit', array(
            'do'                   => 'edit',
            'cancel_url'           => ($back_url ? $back_url : href_to('users', $profile['id'])),
            'id'                   => $profile['id'],
            'profile'              => $profile,
            'form'                 => $form,
            'allow_delete_profile' => $allow_delete_profile,
            'errors'               => isset($errors) ? $errors : false
        ));

    }

}
