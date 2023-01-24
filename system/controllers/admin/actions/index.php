<?php

class actionAdminIndex extends cmsAction {

    public function run() {

        //
        // формируем виджеты главной админки
        //
        $result_dashboard_blocks = [];

        if (empty($this->options['dashboard_enabled'])) {
            $this->setOption('dashboard_enabled', []);
        }

        if (empty($this->options['dashboard_order'])) {
            $this->setOption('dashboard_order', []);
        }

        $dashboard_blocks = cmsEventsManager::hookAll('admin_dashboard_block', $this->options, []);

        // по умолчанию порядок заведомо большой
        $order_id = 1000;

        foreach ($dashboard_blocks as $cname => $dashboard_block) {

            // в одном хуке можно создавать несколько виджетов админки
            // для этого хук должен вернуть массив виджетов
            if (isset($dashboard_block['title'])) {
                $dashboard_block = [$dashboard_block];
            }

            foreach ($dashboard_block as $key => $sub_dashboard_block) {

                if (!isset($sub_dashboard_block['name'])) {
                    $sub_dashboard_block['name'] = $cname . '_' . $key;
                }

                if (isset($this->options['dashboard_order'][$sub_dashboard_block['name']])) {
                    $order_id = $this->options['dashboard_order'][$sub_dashboard_block['name']];
                }

                $result_dashboard_blocks[$order_id] = $sub_dashboard_block;

                if (!isset($this->options['dashboard_order'][$sub_dashboard_block['name']])) {
                    $order_id += 1;
                }
            }
        }

        ksort($result_dashboard_blocks);

        return $this->cms_template->render('index', [
            'dashboard_blocks' => $result_dashboard_blocks
        ]);
    }

}
