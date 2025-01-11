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

            if (!$errors) {
                 list($values, $errors) = $this->checkCacheHandler($values, $errors);
            }

            if (!$errors) {

                if($values['db_charset'] !== $this->cms_config->db_charset){

                    $collations = [
                        'utf8' => 'utf8_general_ci',
                        'utf8mb3' => 'utf8mb3_general_ci',
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

                        cmsUser::addSessionMessage(LANG_CP_SETTINGS_DB_CHARSET_ERROR, 'error');

                        // Не меняем, если не поддерживается
                        $values['db_charset'] = $this->cms_config->db_charset;
                    }

                }

                $values = cmsEventsManager::hook('site_settings_before_update', $values);

                $result = $this->cms_config->save($values);

                if (!$result){

                    $errors = [];

                    cmsUser::addSessionMessage(LANG_CP_SETTINGS_NOT_WRITABLE, 'error');

                } else {

                    cmsEventsManager::hook('site_settings_after_update', $values);

                    cmsUser::addSessionMessage(LANG_CP_SAVE_SUCCESS, 'success');

                    return $this->redirectToAction('settings');
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

        return $this->cms_template->render('settings', [
            'templates_has_options' => $templates_has_options,
            'do'     => 'edit',
            'values' => $values,
            'form'   => $form,
            'errors' => $errors ?? false
        ]);
    }

    private function checkCacheHandler($values, $errors) {

        // Новый конфиг согласно настройкам
        $new_config = clone $this->cms_config;
        $new_config->setData(array_merge($this->cms_config->getConfig(), $values));

        // Сессии
        switch ($values['session_save_handler']) {
            case 'files':

                if(!is_dir($values['session_save_path'])){
                    if(!mkdir($values['session_save_path'], 0755, true)){
                        $errors['session_save_path'] = LANG_CP_FTP_MKDIR_FAILED;
                    }
                }

                if (!is_writable($values['session_save_path'])) {
                    $errors['session_save_path'] = sprintf(LANG_CP_INSTALL_NOT_WRITABLE, $values['session_save_path']);
                }

                break;

            default:

                list($host, $port) = explode(':', str_replace('tcp://', '', $values['session_save_path']));

                $new_config->cache_method = $values['session_save_handler'];
                $new_config->cache_host   = $host;
                $new_config->cache_port   = $port;

                $cacher = cmsCache::getCacher($new_config);

                $check = $this->checkCacher($cacher);

                if ($check < 1) {
                    $errors['session_save_handler'] = sprintf(
                        ($check === -1 ? LANG_CP_CACHE_MOD_NOT_AVAILABLE : LANG_CP_CACHE_MOD_CONNECT_ERROR),
                        $values['session_save_handler']
                    );
                }

                break;
        }

        if ($errors) {
            return [$values, $errors];
        }

        // Кэширование

        $new_config->cache_method = $values['cache_method'];
        $new_config->cache_host   = $values['cache_host'];
        $new_config->cache_port   = $values['cache_port'];

        $cacher = cmsCache::getCacher($new_config);

        $check = $this->checkCacher($cacher);

        if ($check < 1) {
            $errors['cache_method'] = sprintf(
                ($check === -1 ? LANG_CP_CACHE_MOD_NOT_AVAILABLE : LANG_CP_CACHE_MOD_CONNECT_ERROR),
                $values['cache_method']
            );
        }

        if ($errors) {
            return [$values, $errors];
        }

        // Был включен кэш, выключили
        if (!$values['cache_enabled'] && $this->cms_config->cache_enabled) {

            $this->cms_cache->clean();
            $this->cms_cache->stop();
        }

        return [$values, $errors];
    }

    private function checkCacher($cacher) {

        if (!$cacher->isDependencySatisfied()) {
            return -1;
        }

        try {

            $cacher->start();

            $success = $cacher->testConnection();

        } catch (Throwable $e) {

            $success = 0;
        }

        return $success;
    }

}
