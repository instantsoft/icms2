<?php

class actionAdminSettingsSysInfo extends cmsAction {

    public function run() {

        if ($this->cms_config->disable_sys_info) {

            return cmsCore::error404();
        }

        $uploader   = new cmsUploader();
        $extensions = get_loaded_extensions();

        // какой веб сервер
        if (PHP_SAPI == 'apache2handler') {

            // проверяем не двухуровневая ли конфигурация
            $nginx_v = false;
            if ($this->isNginx()) {
                $_nginx_v = console_exec_command('nginx -v');
                if ($_nginx_v) {
                    $nginx_v = trim(preg_replace('#[^0-9\.]#i', '', $_nginx_v[0]));
                }
            }

            $apache_v = (function_exists('apache_get_version') ? apache_get_version() : $this->request->getServer('SERVER_SOFTWARE'));

            $server_data = [
                'title' => LANG_CP_DASHBOARD_SI_WS,
                'value' => $apache_v . ($nginx_v ? ', ' . sprintf(LANG_CP_DASHBOARD_APACHE_NGINX, $nginx_v) : '')
            ];

        } else {

            $server_data = [
                'title' => LANG_CP_DASHBOARD_SI_WS,
                'value' => $this->request->getServer('SERVER_SOFTWARE')
            ];
        }

        $sql_server_info = $this->model->db->getServerInfo();

        $sysinfo = [];

        if (!$this->cms_config->disable_copyright) {

            $sysinfo[LANG_CP_DASHBOARD_SI_ICMS] = cmsCore::getVersion();
        }

        $sysinfo = $sysinfo + [
            $server_data['title']             => $server_data['value'],
            LANG_CP_DASHBOARD_SQL_SERVER      => $sql_server_info['type'].' '.$sql_server_info['version'],
            LANG_CP_DASHBOARD_SI_PHP          => implode('.', [PHP_MAJOR_VERSION, PHP_MINOR_VERSION, PHP_RELEASE_VERSION]) . ', ' . PHP_SAPI,
            LANG_CP_DASHBOARD_SI_ML           => files_format_bytes(files_convert_bytes(@ini_get('memory_limit'))),
            LANG_CP_DASHBOARD_SI_MAX          => $uploader->getMaxUploadSize(),
            LANG_CP_DASHBOARD_SI_ROOT         => PATH,
            LANG_CP_DASHBOARD_SI_SESSION_TYPE => @ini_get('session.save_handler'),
            LANG_CP_DASHBOARD_SI_SESSION      => session_save_path(),
            LANG_CP_DASHBOARD_SI_ZEND         => in_array('Zend OPcache', $extensions),
            LANG_CP_DASHBOARD_SI_ION          => in_array('ionCube Loader', $extensions),
            LANG_CP_DASHBOARD_SI_ZENDG        => in_array('Zend Guard Loader', $extensions)
        ];

        return $this->cms_template->render('index_sysinfo', [
            'sysinfo' => $sysinfo
        ]);
    }

    private function isNginx() {

        $headers = get_headers(cmsConfig::get('host'), 1);
        if (!$headers) {
            return false;
        }

        if (isset($headers['Server'])) {
            if (is_array($headers['Server'])) {
                $headers['Server'] = reset($headers['Server']);
            }
            return strpos(strtolower($headers['Server']), 'nginx') !== false;
        }

        return false;
    }

}
