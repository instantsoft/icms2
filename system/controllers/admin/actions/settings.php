<?php

class actionAdminSettings extends cmsAction {

    public function run($do = false) {

        // если нужно, передаем управление другому экшену
        if ($do){
            $this->runExternalAction('settings_'.$do, array_slice($this->params, 1));
            return;
        }

        $values = $this->cms_config->getConfig();

        $form = $this->getForm('settings');

        if ($this->request->has('submit')){

            $values = array_merge($values, $form->parse($this->request, true));
            $errors = $form->validate($this,  $values);

            if ($values['session_save_handler'] == 'memcache' && !class_exists('Memcache')){

                $errors['session_save_handler'] = LANG_CP_MEMCACHE_NOT_AVAILABLE;

            } else if($values['session_save_handler'] == 'memcached' && !class_exists('Memcached')){

                $errors['session_save_handler'] = LANG_CP_MEMCACHE_NOT_AVAILABLE;

            } else if($values['session_save_handler'] == 'files'){

                if(!is_dir($values['session_save_path'])){
                    if(!mkdir($values['session_save_path'], 0755, true)){
                        $errors['session_save_path'] = LANG_CP_FTP_MKDIR_FAILED;
                    }
                }

                if (!is_writable($values['session_save_path'])) {
                    $errors['session_save_path'] = sprintf(LANG_CP_INSTALL_NOT_WRITABLE, $errors['session_save_path']);
                }

            }

            if (!$errors){

                if ($values['cache_method'] == 'memory'){

                    if (!class_exists('Memcache')){

                        cmsUser::addSessionMessage(LANG_CP_MEMCACHE_NOT_AVAILABLE, 'error');

                        $values['cache_method'] = 'files';

                    } else {

                        $memcache_tester = new Memcache();

                        $memcache_result = $memcache_tester->connect($values['cache_host'], $values['cache_port']);

                        if (!$memcache_result){

                            cmsUser::addSessionMessage(LANG_CP_MEMCACHE_CONNECT_ERROR, 'error');

                            $values['cache_method'] = 'files';

                        }

                    }

                }

                if ($values['cache_method'] == 'memcached'){

                    if (!class_exists('Memcached')){

                        cmsUser::addSessionMessage(LANG_CP_MEMCACHE_NOT_AVAILABLE, 'error');

                        $values['cache_method'] = 'files';

                    } else {

                        $memcache_tester = new Memcached();

                        $memcache_tester->setOption(\Memcached::OPT_BINARY_PROTOCOL, true);

                        $memcache_result = $memcache_tester->addServer($values['cache_host'], $values['cache_port']);

                        if (!$memcache_result){

                            cmsUser::addSessionMessage(LANG_CP_MEMCACHE_CONNECT_ERROR, 'error');

                            $values['cache_method'] = 'files';

                        }

                    }

                }

                if (!$values['cache_enabled']){

                    $cacher = cmsCache::getCacher((object)array_merge($this->cms_config->getAll(), $values));

                    $cacher->start();
                        $cacher->clean();
                    $cacher->stop();

                }

                if($values['db_charset'] !== $this->cms_config->db_charset){

                    $collations = [
                        'utf8' => 'utf8_general_ci',
                        'utf8mb4' => 'utf8mb4_general_ci'
                    ];

                    $collation_name = $collations[$values['db_charset']];

                    $r = $this->model->db->query("SELECT 1 FROM `information_schema`.`COLLATIONS` WHERE `COLLATION_NAME` = '{$collation_name}' AND `CHARACTER_SET_NAME` = '{$values['db_charset']}' AND `IS_COMPILED` = 'Yes'");

                    // если кодировка поддерживается, меняем для таблиц
                    if ($r && $this->model->db->numRows($r)) {

                        $sql = "SELECT CONCAT('ALTER TABLE `', t.`TABLE_SCHEMA`, '`.`', t.`TABLE_NAME`, '` CONVERT TO CHARACTER SET {$values['db_charset']} COLLATE {$collation_name};') as sqlcode FROM `information_schema`.`TABLES` t WHERE 1 AND t.`TABLE_SCHEMA` = '{$this->cms_config->db_base}'";

                        $res = $this->model->db->query($sql, false, true);
                        if($res !== false){

                            while($item = $this->model->db->fetchAssoc($res)){
                                $this->model->db->query($item['sqlcode'], false, true);
                            }

                            $this->model->db->freeResult($res);

                        }

                        // меняем кодировку по умолчанию для базы
                        $this->model->db->query("ALTER DATABASE {$this->cms_config->db_base} CHARACTER SET {$values['db_charset']} COLLATE {$collation_name}");

                    } else {
                        // Не меняем, если не поддерживается
                        $values['db_charset'] = $this->cms_config->db_charset;
                    }

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

        $templates_has_options = [];

        $tpls = cmsCore::getTemplates();
        foreach ($tpls as $tpl) {
            $template_path = $this->cms_config->root_path . cmsTemplate::TEMPLATE_BASE_PATH. $tpl;
            $manifest = cmsTemplate::getTemplateManifest($template_path);
            if($manifest !== null){
                if (!empty($manifest['properties']['has_options'])) {
                    $templates_has_options[] = $tpl;
                }
                continue;
            }
            // Совместимость
            if(file_exists($template_path.'/options.form.php')){
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
