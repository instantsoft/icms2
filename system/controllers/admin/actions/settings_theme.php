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

                list($template_name, $options) = cmsEventsManager::hook('template_before_save_options', [$template_name, $options]);
                $options = cmsEventsManager::hook('template_'.$template_name.'_before_save_options', $options);

                if($template->saveOptions($options)){
                    cmsUser::addSessionMessage(LANG_CP_SAVE_SUCCESS, 'success');
                } else {
                    cmsUser::addSessionMessage(LANG_CP_SETTINGS_TPL_NOT_WRITABLE, 'error');
                }

                $this->redirectToAction('settings', ['theme', $template_name]);

            }

            if ($errors){
                cmsUser::addSessionMessage(LANG_FORM_ERRORS, 'error');
            }

        }

        if($template_name !== 'default'){

            $inherit_templates = $this->cms_template->getInheritTemplates();
            $inherit_templates[] = $template_name;

            $inherit_templates = array_unique($inherit_templates);

            $this->cms_template->setInheritNames($inherit_templates);
        }

        return $this->cms_template->render('settings_theme', array(
            'template_name' => $template_name,
            'options'       => $options,
            'form'          => $form,
            'errors'        => isset($errors) ? $errors : false
        ));

    }

}
