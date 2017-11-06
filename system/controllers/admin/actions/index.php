<?php

class actionAdminIndex extends cmsAction {

    public function run(){

        //
        // формируем виджеты главной админки
        //

        // Виджет статистики

        $chart_nav = cmsEventsManager::hookAll('admin_dashboard_chart');

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

        $dashboard_blocks[] = array(
            'title' => LANG_CP_DASHBOARD_STATS,
            'class' => 'col3',
            'html'  => $this->cms_template->getRenderedChild('index_chart', array(
                'chart_nav' => $chart_nav,
                'defaults'  => $defaults
            ))
        );

        $uploader   = new cmsUploader();
        $extensions = get_loaded_extensions();

        $sysinfo = array(
            LANG_CP_DASHBOARD_SI_ICMS  => cmsCore::getVersion(),
            LANG_CP_DASHBOARD_SI_PHP   => phpversion(),
            LANG_CP_DASHBOARD_SI_ML    => files_format_bytes(files_convert_bytes(@ini_get('memory_limit'))),
            LANG_CP_DASHBOARD_SI_MAX   => $uploader->getMaxUploadSize(),
            LANG_CP_DASHBOARD_SI_IP    => filter_input(INPUT_SERVER, 'SERVER_ADDR'),
            LANG_CP_DASHBOARD_SI_ROOT  => PATH,
            LANG_CP_DASHBOARD_SI_SESSION => session_save_path(),
            LANG_CP_DASHBOARD_SI_ION   => in_array('ionCube Loader', $extensions),
            LANG_CP_DASHBOARD_SI_ZEND  => in_array('Zend Optimizer', $extensions),
            LANG_CP_DASHBOARD_SI_ZENDG => in_array('Zend Guard Loader', $extensions)
        );

        $dashboard_blocks[] = array(
            'title' => LANG_CP_DASHBOARD_NEWS,
            'html'  => $this->cms_template->getRenderedChild('index_news', array())
        );

        $dashboard_blocks[] = array(
            'title' => LANG_CP_DASHBOARD_SYSINFO,
            'html'  => $this->cms_template->getRenderedChild('index_sysinfo', array(
                'sysinfo' => $sysinfo
            ))
        );

        $dashboard_blocks[] = array(
            'title' => LANG_CP_DASHBOARD_RESOURCES,
            'html'  => $this->cms_template->getRenderedChild('index_resources', array())
        );

        $dashboard_blocks = array_merge($dashboard_blocks, cmsEventsManager::hookAll('admin_dashboard_block', false, array()));

        $_block_id = 0;
        foreach ($dashboard_blocks as $dashboard_block) {
            // в одном хуке можно создавать несколько виджетов админки
            // для этого хук должен вернуть массив виджетов
            if(!isset($dashboard_block['title'])){
                foreach ($dashboard_block as $sub_dashboard_block) {
                    $sub_dashboard_block['id'] = $_block_id;
                    $result_dashboard_blocks[$_block_id] = $sub_dashboard_block;
                    $_block_id++;
                }
            } else {
                $dashboard_block['id'] = $_block_id;
                $result_dashboard_blocks[$_block_id] = $dashboard_block;
            }
            $_block_id++;
        }

        // формируем с учетом порядка
        if(!empty($this->options['dashboard_order'])){
            $order_id = 1000;
            foreach ($result_dashboard_blocks as $block_id => $block) {
                if(isset($this->options['dashboard_order'][$block_id])){
                    $order_id = $this->options['dashboard_order'][$block_id];
                } else {
                    $order_id += 1;
                }
                $_result_dashboard_blocks[$order_id] = $block;
            }
            ksort($_result_dashboard_blocks);
        } else {
            $_result_dashboard_blocks = $result_dashboard_blocks;
        }

        return $this->cms_template->render('index', array(
            'dashboard_blocks' => $_result_dashboard_blocks
        ));

    }

}
