<?php

class actionUsersProfileEditTheme extends cmsAction {

    public $lock_explicit_call = true;

    public function run($profile){

        // проверяем наличие доступа
        if (!$this->is_own_profile && !$this->cms_user->is_admin) { cmsCore::error404(); }

        if (!$this->cms_template->hasProfileThemesOptions()){ cmsCore::error404(); }

        $form = $this->cms_template->getProfileOptionsForm();

        // Форма отправлена?
        $is_submitted = $this->request->has('submit');

        $theme = $profile['theme'];

        if ($is_submitted){

            // Парсим форму и получаем поля записи
            $theme = array_merge($theme, $form->parse($this->request, $is_submitted, $theme));

            // Проверям правильность заполнения
            $errors = $form->validate($this,  $theme);

            if (!$errors){

                // Обновляем профиль и редиректим на его просмотр
                $this->model->updateUserTheme($profile['id'], $theme);

                cmsUser::addSessionMessage(LANG_SUCCESS_MSG, 'success');

                $this->redirectTo('users', $profile['id']);

            }

            if ($errors){
                cmsUser::addSessionMessage(LANG_FORM_ERRORS, 'error');
            }

        }

        return $this->cms_template->render('profile_edit_theme', array(
            'id'      => $profile['id'],
            'profile' => $profile,
            'form'    => $form,
            'errors'  => isset($errors) ? $errors : false
        ));

    }

}
