<?php

class actionAdminSettingsTheme extends cmsAction {

    public function run($template_name){

        $template = new cmsTemplate($template_name);
        if (!$template->hasOptions()){ cmsCore::error404(); }

        $form = $template->getOptionsForm();

        $options = $template->getOptions();

        if ($this->request->has('submit')){

            // Парсим форму и получаем поля записи
            $options = $form->parse($this->request, true, $options);

            // Проверям правильность заполнения
            $errors = $form->validate($this,  $options);

            if (!$errors){

                if($template->saveOptions($options)){
                    cmsUser::addSessionMessage(LANG_CP_SAVE_SUCCESS, 'success');
                } else {
                    cmsUser::addSessionMessage(LANG_CP_SETTINGS_TPL_NOT_WRITABLE, 'error');
                }

                $this->redirectToAction('settings');

            }

            if ($errors){
                cmsUser::addSessionMessage(LANG_FORM_ERRORS, 'error');
            }

        }

        $this->cms_template->setName($template_name);

        $this->cms_template->setInheritNames($this->cms_template->getInheritTemplates());

        return $this->cms_template->render('settings_theme', array(
            'template_name' => $template_name,
            'options'       => $options,
            'form'          => $form,
            'errors'        => isset($errors) ? $errors : false
        ));

    }

}