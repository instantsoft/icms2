<?php

class actionUsersProfileEdit extends cmsAction {

    public $lock_explicit_call = true;

    public function run($profile, $do = false, $param = false) {

        if (!$this->cms_user->is_logged) {
            cmsCore::error404();
        }

        // если нужно, передаем управление другому экшену
        if ($do) {
            $this->runExternalAction('profile_edit_' . $do, array($profile) + array_slice($this->params, 2, null, true));
            return;
        }

        $back_url = $this->getRequestBackUrl();

        // проверяем наличие доступа
        if (!$this->is_own_profile && !$this->cms_user->is_admin) {
            cmsCore::error404();
        }

        // Получаем поля
        $content_model = cmsCore::getModel('content');
        $content_model->setTablePrefix('');
        $content_model->orderBy('ordering');
        $fields = $content_model->getContentFields('{users}', $profile['id']);

        // Строим форму
        $form = new cmsForm();

        // Разбиваем поля по группам
        $fieldsets = cmsForm::mapFieldsToFieldsets($fields, function($field, $user) use ($profile) {

            // проверяем что группа пользователя имеет доступ к редактированию этого поля
            if ($field['groups_edit'] && !$user->isInGroups($field['groups_edit'])) {
                // если группа пользователя не имеет доступ к редактированию этого поля,
                // проверяем на доступ к нему для авторов
                if (!empty($profile['id']) && !empty($field['options']['author_access'])) {
                    if (!in_array('is_edit', $field['options']['author_access'])) {
                        return false;
                    }
                    if ($profile['id'] == $user->id) {
                        return true;
                    }
                }
                return false;
            }
            return true;
        });

        // Добавляем поля в форму
        foreach ($fieldsets as $fieldset) {

            $fid = $fieldset['title'] ? md5($fieldset['title']) : null;

            $fieldset_id = $form->addFieldset($fieldset['title'], $fid);

            foreach ($fieldset['fields'] as $field) {
                // добавляем поле в форму
                $form->addField($fieldset_id, $field['handler']);
            }
        }

        // Добавляем поле выбора часового пояса
        if ($this->cms_config->allow_users_time_zone) {
            $fieldset_id = $form->addFieldset(LANG_TIME_ZONE);
            $form->addField($fieldset_id, new fieldList('time_zone', [
                'default'   => $this->cms_config->time_zone,
                'generator' => function($item) {

                    $zones = (new cmsConfigs('timezones.php'))->getAll();

                    return array_combine($zones, $zones);
                }
            ]));
        }

        // Добавляем поле SLUG
        if (cmsUser::isAllowed('users', 'change_slug')) {
            $fieldset_id = $form->addFieldset(LANG_USERS_SLUG);
            $form->addField($fieldset_id, new fieldString('slug', [
                'hint'  => ERR_VALIDATE_SLUGS,
                'prefix'  => href_to_abs('users') . '/',
                'options' => [
                    'min_length' => 2,
                    'max_length' => 100
                ],
                'rules'   => array(
                    ['slug_segment'],
                    ['unique_exclude', '{users}', 'slug', $profile['id']],
                    [function($controller, $data, $value) {

                        $datasets = $this->getDatasets();

                        if (isset($datasets[$value])) {
                            return sprintf(LANG_USERS_OPT_RESTRICTED_SLUG, $value);
                        }
                        if (!$this->isSlugAllowed($value)) {
                            return sprintf(LANG_USERS_OPT_RESTRICTED_SLUG, $value);
                        }

                        return true;
                    }]
                )
            ]));
        }

        list($form, $profile, $fields) = cmsEventsManager::hook('users_profile_edit_form', [$form, $profile, $fields]);

        // Форма отправлена?
        $is_submitted = $this->request->has('submit');

        if ($is_submitted) {

            // Парсим форму и получаем поля записи
            $new     = $form->parse($this->request, $is_submitted, $profile);
            $old     = $profile;
            $profile = array_merge($profile, $new);

            // Проверям правильность заполнения
            $errors = $form->validate($this, $profile);

            if (!$errors) {
                $is_allowed = cmsEventsManager::hookAll('user_profile_update', $profile, true);
                if (is_array($is_allowed)) {
                    $errors = [];
                    foreach ($is_allowed as $error_list) {
                        if (is_array($error_list) && $error_list) {
                            $errors = array_merge($error_list);
                        }
                    }
                }
            }

            if (!$errors) {

                list($profile, $old) = cmsEventsManager::hook('users_before_update', [$profile, $old, $fields]);

                // Обновляем профиль и редиректим на его просмотр
                $this->model->updateUser($profile['id'], $profile);

                $this->model->fieldsAfterStore($profile, $fields, 'edit');

                list($profile, $old) = cmsEventsManager::hook('users_after_update', [$profile, $old, $fields]);

                // Отдельно обновляем часовой пояс в сессии
                cmsUser::sessionSet('user:time_zone', $profile['time_zone']);

                $content = cmsCore::getController('content', $this->request);

                $parents = $content->model->getContentTypeParents(null, $this->name);

                if ($parents) {
                    $content->bindItemToParents(['id' => null, 'name' => $this->name, 'controller' => $this->name], $profile, $parents);
                }

                cmsUser::addSessionMessage(LANG_SUCCESS_MSG, 'success');

                if ($back_url) {
                    $this->redirect($back_url);
                } else {
                    $this->redirectTo('users', (empty($profile['slug']) ? $profile['id'] : $profile['slug']));
                }
            }

            if ($errors) {
                cmsUser::addSessionMessage(LANG_FORM_ERRORS, 'error');
            }
        }

        $allow_delete_profile = (cmsUser::isAllowed('users', 'delete', 'any') ||
                (cmsUser::isAllowed('users', 'delete', 'my') && $this->is_own_profile));

        return $this->cms_template->render('profile_edit', [
            'do'                   => 'edit',
            'cancel_url'           => ($back_url ? $back_url : href_to_profile(!empty($old) ? $old : $profile)),
            'id'                   => $profile['id'],
            'profile'              => $profile,
            'form'                 => $form,
            'allow_delete_profile' => $allow_delete_profile,
            'errors'               => isset($errors) ? $errors : false
        ]);
    }

}
