<?php

class actionAdminSettingsTheme extends cmsAction {

    public function run($template_name){

        $template = new cmsTemplate($template_name);

        if (!$template->hasOptions()){ cmsCore::error404(); }

        $form = $template->getOptionsForm();

        // Форма отправлена?
        $is_submitted = $this->request->has('submit');

        $options = $template->getOptions();

        if ($is_submitted){

            // Парсим форму и получаем поля записи
            $options = $form->parse($this->request, $is_submitted, $options);

            // Проверям правильность заполнения
            $errors = $form->validate($this,  $options);

            if (!$errors){

                $template->saveOptions($options);
                
                $this->redirectBack('settings');

            }

            if ($errors){
                cmsUser::addSessionMessage(LANG_FORM_ERRORS, 'error');
            }

        }

        return cmsTemplate::getInstance()->render('settings_theme', array(
            'template_name' => $template_name,
            'options' => $options,
            'form' => $form,
            'errors' => isset($errors) ? $errors : false
        ));

    }

}
