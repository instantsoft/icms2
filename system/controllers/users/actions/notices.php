<?php

class actionUsersNotices extends cmsAction {

    public function run($id){
		
		if (!cmsUser::isLogged()) { cmsCore::error404(); }

        if (!$id) { cmsCore::error404(); }

        $user = cmsUser::getInstance();

        // Получаем нужную запись
        $profile = $this->model->getUser($id);

        // проверяем наличие доступа
        if ($id != $user->id && !$user->is_admin) { cmsCore::error404(); }

        $template = cmsTemplate::getInstance();

        if (!$template->hasProfileThemesOptions()){ cmsCore::error404(); }

        cmsCore::loadTemplateLanguage($template->getName());

        $form = $template->getProfileOptionsForm();

        // Форма отправлена?
        $is_submitted = $this->request->has('submit');

        $theme = $profile['theme'];

        if ($is_submitted){

            // Парсим форму и получаем поля записи
            $theme = array_merge($theme, $form->parse($this->request, $is_submitted, $theme));

            // Проверям правильность заполнения
            $errors = $form->validate($this,  $profile);

            if (!$errors){

                $profile['theme'] = $theme;

                // Обновляем профиль и редиректим на его просмотр
                $this->model->updateUser($id, $profile);

                $this->redirectTo('users', $id);

            }

            if ($errors){
                cmsUser::addSessionMessage(LANG_FORM_ERRORS, 'error');
            }

        }

        return $template->render('profile_theme', array(
            'id' => $id,
            'profile' => $profile,
            'form' => $form,
            'errors' => isset($errors) ? $errors : false
        ));

    }

}
