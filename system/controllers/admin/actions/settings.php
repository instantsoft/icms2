<?php

class actionAdminSettings extends cmsAction {

    public function run($do=false){

        // если нужно, передаем управление другому экшену
        if ($do){
            $this->runAction('settings_'.$do, array_slice($this->params, 1));
            return;
        }

        $config = cmsConfig::getInstance();

        $values = $config->getAll();
        $values['time_zone'] = $values['cfg_time_zone'];

        $form = $this->getForm('settings');

        $is_submitted = $this->request->has('submit');

        if ($is_submitted){

            $values = array_merge($values, $form->parse($this->request, $is_submitted));
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

                $result = $config->save($values);

                if (!$result){
                    $errors = array();
                    cmsUser::addSessionMessage(LANG_CP_SETTINGS_NOT_WRITABLE, 'error');
                } else {
                    $this->redirectBack();
                }

            } else {

                cmsUser::addSessionMessage(LANG_FORM_ERRORS, 'error');

            }

        }

        return cmsTemplate::getInstance()->render('settings', array(
            'do' => 'edit',
            'values' => $values,
            'form' => $form,
            'errors' => isset($errors) ? $errors : false
        ));

    }

}
