<?php

class actionAdminSettingsTheme extends cmsAction {

    public function run($template_name){

        $template = new cmsTemplate($template_name);

        $form = $template->getOptionsForm();
        if (!$form){ cmsCore::error404(); }

        $options = $template->getOptions();

        $is_submitted = $this->request->has('submit') || $this->request->has('submit_compile');

        if ($is_submitted){

            // Парсим форму и получаем поля записи
            $options = $form->parse($this->request, true, $options);

            // Проверям правильность заполнения
            $errors = $form->validate($this,  $options);

            if (!$errors){

                list($template_name, $options) = cmsEventsManager::hook('template_before_save_options', [$template_name, $options]);
                $options = cmsEventsManager::hook('template_'.$template_name.'_before_save_options', $options);

                // Если нужно компилировать и шаблон поддерживает SCSS, компилируем
                if($this->request->has('submit_compile')){

                    $manifest = $template->getManifest();

                    if($manifest !== null && !empty($manifest['properties']['style_middleware'])){

                        $renderer = cmsCore::getController('renderer', new cmsRequest([
                            'middleware' => $manifest['properties']['style_middleware']
                        ]), cmsRequest::CTX_INTERNAL);

                        $renderer->cms_template = $template;

                        $renderer->render($template_name, $options);
                    }
                }

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

        $template_file = $this->cms_config->root_path.cmsTemplate::TEMPLATE_BASE_PATH.$template_name.'/controllers/admin/settings_theme.tpl.php';
        $template_data = [
            'manifest'      => $template->getManifest(),
            'template_name' => $template_name,
            'options'       => $options,
            'form'          => $form,
            'errors'        => isset($errors) ? $errors : false
        ];

        if(is_readable($template_file)){
            return $this->cms_template->processRender($template_file, $template_data, false, true);
        }

        return $this->cms_template->render('settings_theme', $template_data);
    }

}
