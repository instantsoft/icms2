<?php

class actionAdminIndex extends cmsAction {

    public function run(){

        $chart_nav = cmsEventsManager::hookAll('admin_dashboard_chart');

        $uploader   = new cmsUploader();
        $extensions = get_loaded_extensions();

        $sysinfo = array(
            LANG_CP_DASHBOARD_SI_PHP   => phpversion(),
            LANG_CP_DASHBOARD_SI_ML    => files_format_bytes(files_convert_bytes(@ini_get('memory_limit'))),
            LANG_CP_DASHBOARD_SI_MAX   => $uploader->getMaxUploadSize(),
            LANG_CP_DASHBOARD_SI_IP    => filter_input(INPUT_SERVER, 'SERVER_ADDR'),
            LANG_CP_DASHBOARD_SI_ROOT  => ROOT,
            LANG_CP_DASHBOARD_SI_ION   => in_array('ionCube Loader', $extensions),
            LANG_CP_DASHBOARD_SI_ZEND  => in_array('Zend Optimizer', $extensions),
            LANG_CP_DASHBOARD_SI_ZENDG => in_array('Zend Guard Loader', $extensions)
        );

        $cookie = cmsUser::getCookie('dashboard_chart');

        $defaults = array(
            'controller' => 'users',
            'section'    => 'reg',
            'period'     => 7
        );

        if ($cookie){
            $cookie = json_decode($cookie, true);
            if(is_array($cookie)){
                $defaults = array(
                    'controller' => $cookie['c'],
                    'section'    => $cookie['s'],
                    'period'     => $cookie['p']
                );
            }
        }

        return cmsTemplate::getInstance()->render('index', array(
            'dashboard_blocks' => cmsEventsManager::hookAll('admin_dashboard_block'),
            'chart_nav' => $chart_nav,
            'sysinfo'   => $sysinfo,
            'defaults'  => $defaults
        ));

    }

}