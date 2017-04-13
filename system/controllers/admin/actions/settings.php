<?php

class actionAdminSettings extends cmsAction {

    public function run($do=false){

        // если нужно, передаем управление другому экшену
        if ($do){
            $this->runAction('settings_'.$do, array_slice($this->params, 1));
            return;
        }

        $values = $this->cms_config->getAll();
        $values['time_zone'] = $values['cfg_time_zone'];

        $form = $this->getForm('settings');

        if ($this->request->has('submit')){

            $values = array_merge($values, $form->parse($this->request, true));
            $errors = $form->validate($this,  $values);

            if (!$errors){

                if ($values['cache_method'] == 'memory'){
                    if (!class_exists('Memcache')){
                        cmsUser::addSessionMessage(LANG_CP_MEMCACHE_NOT_AVAILABLE, 'error');
                        $values['cache_method'] = 'files';
                    }
                }

                if ($values['cache_method'] == 'memory'){
                    $memcache_tester = new Memcache;
                    $memcache_result = @$memcache_tester->connect($values['cache_host'], $values['cache_port']);
                    if (!$memcache_result){
                        cmsUser::addSessionMessage(LANG_CP_MEMCACHE_CONNECT_ERROR, 'error');
                        $values['cache_method'] = 'files';
                    }
                }

                if (!$values['cache_enabled'] && $values['cache_method'] == 'files'){
                    files_clear_directory($this->cms_config->cache_path.'data/');
                }

                $values = cmsEventsManager::hook('site_settings_before_update', $values);

                $result = $this->cms_config->save($values);

                if (!$result){

                    $errors = array();

                    cmsUser::addSessionMessage(LANG_CP_SETTINGS_NOT_WRITABLE, 'error');

                } else {

                    cmsEventsManager::hook('site_settings_after_update', $values);

                    cmsUser::addSessionMessage(LANG_CP_SAVE_SUCCESS, 'success');

                    $this->redirectToAction('settings');

                }

            } else {

                cmsUser::addSessionMessage(LANG_FORM_ERRORS, 'error');

            }

        }

        $tpls = cmsCore::getTemplates();
        foreach ($tpls as $tpl) {
            if(file_exists($this->cms_config->root_path.'templates/'.$tpl.'/options.form.php')){
                $templates_has_options[] = $tpl;
            }
        }

        return $this->cms_template->render('settings', array(
            'templates_has_options' => $templates_has_options,
            'do'     => 'edit',
            'values' => $values,
            'form'   => $form,
            'errors' => isset($errors) ? $errors : false
        ));

    }

}
