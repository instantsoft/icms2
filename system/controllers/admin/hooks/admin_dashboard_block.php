<?php

class onAdminAdminDashboardBlock extends cmsAction {

    public function run($options) {

        // Можно отключить упоминания InstantCMS в конфиге
        $disable_copyright = cmsConfig::get('disable_copyright');

        if (!empty($options['only_titles'])) {

            $titles = [
                'stat' => LANG_CP_DASHBOARD_STATS
            ];

            if (!$disable_copyright) {

                $titles['news'] = LANG_CP_DASHBOARD_NEWS;

                $titles['resources'] = LANG_CP_DASHBOARD_RESOURCES;
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
        if (!$disable_copyright &&
                (!array_key_exists('news', $options['dashboard_enabled']) || !empty($options['dashboard_enabled']['news']))) {

            $dashboard_blocks[] = [
                'title' => LANG_CP_DASHBOARD_NEWS,
                'name'  => 'news',
                'html'  => $this->cms_template->getRenderedChild('index_news')
            ];
        }

        // ресурсы icms
        if (!$disable_copyright &&
                (!array_key_exists('resources', $options['dashboard_enabled']) || !empty($options['dashboard_enabled']['resources']))) {

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
