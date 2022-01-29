<?php

class actionAdminSettingsTheme extends cmsAction {

    public function run($template_name, $do = false){

        $tpls = cmsCore::getTemplates();
        if (!in_array($template_name, $tpls)) {
            return cmsCore::error404();
        }

        // если нужно, передаем управление другому экшену
        if ($do) {
            $this->runExternalAction('settings_theme_' . $do, [$template_name] + array_slice($this->params, 2));
            return;
        }

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

                        // Если задан абстрактный счётчик, увеличиваем на единицу
                        // Если он не задан, то вероятно администратор сайта это сделал
                        // осознано для самостоятельной отладки
                        if ($this->cms_config->production_time > 0) {

                            $values = $this->cms_config->getConfig();
                            $values['production_time'] += 1;

                            $this->cms_config->save($values);
                        }
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
