<?php

class onAdminAdminDashboardBlock extends cmsAction {

    public function run($options) {

        if (!empty($options['only_titles'])) {

            $titles = [
                'stat'      => LANG_CP_DASHBOARD_STATS,
                'news'      => LANG_CP_DASHBOARD_NEWS,
                'resources' => LANG_CP_DASHBOARD_RESOURCES,
            ];

            // совместимость
            // в новом шаблоне этого виджета нет
            if ($this->cms_template->name !== 'admincoreui') {
                $titles['sysinfo'] = LANG_CP_DASHBOARD_SYSINFO;
            }

            return $titles;
        }

        $dashboard_blocks = [];

        // Виджет статистики
        if (!array_key_exists('stat', $options['dashboard_enabled']) || !empty($options['dashboard_enabled']['stat'])) {

            $chart_nav = cmsEventsManager::hookAll('admin_dashboard_chart');

            $cookie = cmsUser::getCookie('dashboard_chart');

            $defaults = [
                'type'       => 'bar',
                'controller' => 'users',
                'section'    => 'reg',
                'interval'   => '7:DAY',
                'period'     => 7
            ];

            if ($cookie) {
                $cookie = json_decode($cookie, true);
                if (is_array($cookie)) {
                    $defaults = [
                        'type'       => !empty($cookie['t']) ? $cookie['t'] : 'bar',
                        'controller' => !empty($cookie['c']) ? $cookie['c'] : 'users',
                        'section'    => !empty($cookie['s']) ? $cookie['s'] : 'reg',
                        'interval'   => !empty($cookie['i']) ? $cookie['i'] : '7:DAY',
                        'period'     => !empty($cookie['p']) ? (int)$cookie['p'] : 7
                    ];
                }
            }

            $dashboard_blocks[] = [
                'title'      => LANG_CP_DASHBOARD_STATS,
                'hide_title' => true, // работает на новом шаблоне админки
                'class'      => 'col-12 col3',
                'name'       => 'stat',
                'html'       => $this->cms_template->getRenderedChild('index_chart', [
                    'chart_nav' => $chart_nav,
                    'defaults'  => $defaults
                ])
            ];
        }

        // новости icms
        if (!array_key_exists('news', $options['dashboard_enabled']) || !empty($options['dashboard_enabled']['news'])) {

            $dashboard_blocks[] = [
                'title' => LANG_CP_DASHBOARD_NEWS,
                'name'  => 'news',
                'html'  => $this->cms_template->getRenderedChild('index_news')
            ];
        }

        // Информация о системе
        if ($this->cms_template->name != 'admincoreui' &&
                (!array_key_exists('sysinfo', $options['dashboard_enabled']) || !empty($options['dashboard_enabled']['sysinfo']))) {

            $uploader   = new cmsUploader();
            $extensions = get_loaded_extensions();

            $sysinfo = [
                LANG_CP_DASHBOARD_SI_ICMS         => cmsCore::getVersion(),
                LANG_CP_DASHBOARD_SI_PHP          => implode('.', [PHP_MAJOR_VERSION, PHP_MINOR_VERSION, PHP_RELEASE_VERSION]),
                LANG_CP_DASHBOARD_SI_ML           => files_format_bytes(files_convert_bytes(@ini_get('memory_limit'))),
                LANG_CP_DASHBOARD_SI_MAX          => $uploader->getMaxUploadSize(),
                LANG_CP_DASHBOARD_SI_IP           => filter_input(INPUT_SERVER, 'SERVER_ADDR'),
                LANG_CP_DASHBOARD_SI_ROOT         => PATH,
                LANG_CP_DASHBOARD_SI_SESSION_TYPE => @ini_get('session.save_handler'),
                LANG_CP_DASHBOARD_SI_SESSION      => session_save_path(),
                LANG_CP_DASHBOARD_SI_ZEND         => in_array('Zend OPcache', $extensions),
                LANG_CP_DASHBOARD_SI_ION          => in_array('ionCube Loader', $extensions),
                LANG_CP_DASHBOARD_SI_ZENDG        => in_array('Zend Guard Loader', $extensions)
            ];

            $dashboard_blocks[] = [
                'title' => LANG_CP_DASHBOARD_SYSINFO,
                'name'  => 'sysinfo',
                'html'  => $this->cms_template->getRenderedChild('index_sysinfo', [
                    'sysinfo' => $sysinfo
                ])
            ];
        }

        // ресурсы icms
        if (!array_key_exists('resources', $options['dashboard_enabled']) || !empty($options['dashboard_enabled']['resources'])) {

            $dashboard_blocks[] = [
                'title'       => LANG_CP_DASHBOARD_RESOURCES,
                'child_class' => 'bg-info',
                'name'        => 'resources',
                'html'        => $this->cms_template->getRenderedChild('index_resources')
            ];
        }

        return $dashboard_blocks;
    }

}
