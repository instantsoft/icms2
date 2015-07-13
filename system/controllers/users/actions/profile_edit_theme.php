<?php

class actionUsersProfileEditTheme extends cmsAction {

    public function run($profile){

        $user = cmsUser::getInstance();

        // проверяем наличие доступа
        if ($profile['id'] != $user->id && !$user->is_admin) { cmsCore::error404(); }

        $template = cmsTemplate::getInstance();

        if (!$template->hasProfileThemesOptions()){ cmsCore::error404(); }

        $form = $template->getProfileOptionsForm();

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

                $this->redirectTo('users', $profile['id']);

            }

            if ($errors){
                cmsUser::addSessionMessage(LANG_FORM_ERRORS, 'error');
            }

        }

        return $template->render('profile_edit_theme', array(
            'id' => $profile['id'],
            'profile' => $profile,
            'form' => $form,
            'errors' => isset($errors) ? $errors : false
        ));

    }

}
