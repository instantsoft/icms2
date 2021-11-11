<?php

class actionAdminSettingsSysInfo extends cmsAction {

    public function run(){

        $uploader   = new cmsUploader();
        $extensions = get_loaded_extensions();

        // какой веб сервер
        if (PHP_SAPI == 'apache2handler') {

            // проверяем не двухуровневая ли конфигурация
            $nginx_v = false;
            if($this->isNginx()){
                $_nginx_v = console_exec_command('nginx -v');
                if($_nginx_v){
                    $nginx_v = trim(preg_replace('#[^0-9\.]#i', '', $_nginx_v[0]));
                }
            }

            $apache_v = (function_exists('apache_get_version') ? apache_get_version() : $_SERVER['SERVER_SOFTWARE']);

            $server_data = array(
                'title' => LANG_CP_DASHBOARD_SI_WS,
                'value' => $apache_v.($nginx_v ? ', '.sprintf(LANG_CP_DASHBOARD_APACHE_NGINX, $nginx_v) : '')
            );

        } else {
            $server_data = array(
                'title' => LANG_CP_DASHBOARD_SI_WS,
                'value' => $_SERVER['SERVER_SOFTWARE']
            );
        }

        // mysql
        $o = console_exec_command('mysql -V');

        $mysql_version = 'N/A';
        if($o && is_array($o)){
            preg_match('@[0-9]+\.[0-9]+\.[0-9]+@', current($o), $version);
            if (isset($version[0])) {
                $mysql_version = $version[0];
            }
        }

        $sysinfo = array(
            LANG_CP_DASHBOARD_SI_ICMS  => cmsCore::getVersion(),
            $server_data['title']      => $server_data['value'],
            LANG_CP_DASHBOARD_SQL_SERVER => $mysql_version,
            LANG_CP_DASHBOARD_SI_PHP   => implode('.', array(PHP_MAJOR_VERSION, PHP_MINOR_VERSION, PHP_RELEASE_VERSION)).', '.PHP_SAPI,
            LANG_CP_DASHBOARD_SI_ML    => files_format_bytes(files_convert_bytes(@ini_get('memory_limit'))),
            LANG_CP_DASHBOARD_SI_MAX   => $uploader->getMaxUploadSize(),
            LANG_CP_DASHBOARD_SI_IP    => filter_input(INPUT_SERVER, 'SERVER_ADDR'),
            LANG_CP_DASHBOARD_SI_ROOT  => PATH,
            LANG_CP_DASHBOARD_SI_SESSION_TYPE => @ini_get('session.save_handler'),
            LANG_CP_DASHBOARD_SI_SESSION => session_save_path(),
            LANG_CP_DASHBOARD_SI_ZEND  => in_array('Zend OPcache', $extensions),
            LANG_CP_DASHBOARD_SI_ION   => in_array('ionCube Loader', $extensions),
            LANG_CP_DASHBOARD_SI_ZENDG => in_array('Zend Guard Loader', $extensions)
        );

        return $this->cms_template->render('index_sysinfo', array(
            'sysinfo' => $sysinfo
        ));

    }

    private function isNginx(){

        $headers = get_headers(cmsConfig::get('host'), 1);
        if (!$headers) { return false; }

        if(isset($headers['Server'])){
            if(is_array($headers['Server'])){ $headers['Server'] = reset($headers['Server']); }
            return strpos(strtolower($headers['Server']), 'nginx') !== false;
        }

        return false;
    }

}
