<?php

class actionAdminIndexChartData extends cmsAction {

    private $date_formates = [
        'DAY' => [
            'format' => 'd.m',
            'format_diff' => '-%s days',
            'format_diff_keys' => '-%s days',
            'divisor' => 1
        ],
        'WEEK' => [
            'format' => 'd.m',
            'format_diff' => '-%s week',
            'format_diff_keys' => '-%s days',
            'divisor' => 1
        ],
        'MONTH' => [
            'format' => 'd.m',
            'format_diff' => '-%s months',
            'format_diff_keys' => '-%s days',
            'divisor' => 1
        ],
        'YEAR' => [
            'format' => 'm.Y',
            'format_diff' => '-%s year',
            'format_diff_keys' => '-%s months',
            'divisor' => 30
        ]
    ];

    private $interval = 'DAY';

    private function df($key) {
        return $this->date_formates[$this->interval][$key];
    }

    public function run() {

        if (!$this->request->isAjax()) {
            return cmsCore::error404();
        }

        $id      = $this->request->get('id', '');
        $section = $this->request->get('section', '');
        $period  = $this->request->get('period', 0);

        $this->interval = $this->request->get('interval', '');

        if (!$period && !$this->interval) {
            return cmsCore::error404();
        }

        if ($this->interval) {

            list($period, $this->interval) = explode(':', $this->interval);

            $this->interval = strtoupper($this->interval);

        } else {

            $this->interval = 'DAY';

            if($period >= 365){

                $this->interval = 'YEAR';

                $period = (int)$period/365;
            }
        }

        if (!in_array($this->interval, array_keys($this->date_formates), true)) {
            return cmsCore::error404();
        }

        if (!$id || !$section) {
            return cmsCore::error404();
        }

        $chart_nav = cmsEventsManager::hookAll('admin_dashboard_chart');

        $sources = $old_result = $chart_data = [];

        foreach ($chart_nav as $ctrl) {
            if ($ctrl['id'] == $id) {
                foreach ($ctrl['sections'] as $key => $s) {
                    if ($key == $section || strpos($key, $section . ':') === 0) {
                        $sources[] = $ctrl['sections'][$key];
                    }
                }
            }
        }

        if (!$sources) {
            return cmsCore::error404();
        }

        foreach ($sources as $source) {

            $result = $data_formatted = [];

            $data = $this->getData($source, $period);

            foreach ($data as $item) {
                $data_formatted[date($this->df('format'), strtotime($item['date']))] = (int)$item['count'];
            }

            $diff_time = strtotime(sprintf($this->df('format_diff'), $period));

            $now_date = new DateTime();
            $prev_date = new DateTime('@'.$diff_time);

            $days = (int)$now_date->diff($prev_date)->days/$this->df('divisor');

            for ($d = 0; $d <= $days; $d++) {

                $date = date($this->df('format'), strtotime(sprintf($this->df('format_diff_keys'), $d)));

                $result[$date] = isset($data_formatted[$date]) ? $data_formatted[$date] : 0;
            }

            $result = array_reverse($result);

            // совместимость
            // теперь можно отдавать данные
            // для нескольких графиков
            if (!$old_result) {
                $old_result = $result;
            }

            if (!$chart_data) {
                $chart_data = [
                    'labels'   => array_keys($result),
                    'datasets' => [
                        $this->getDsParams($source, $result)
                    ]
                ];
            } else {
                $chart_data['datasets'][] = $this->getDsParams($source, $result);
            }
        }

        $footer_data = $footer_result = [];

        foreach ($chart_nav as $ctrl) {
            if ($ctrl['id'] == $id && isset($ctrl['footer'][$section])) {
                $footer_data = $ctrl['footer'][$section];
                break;
            }
        }

        if ($footer_data) {
            foreach ($footer_data as $fdata) {
                $footer_result[] = [
                    'count'    => $this->getFooterData($fdata),
                    'title'    => $fdata['title'],
                    'progress' => $fdata['progress']
                ];
            }
        }

        $this->cms_template->renderJSON([
            // это совместимость
            'labels' => array_keys($old_result),
            'values' => array_values($old_result),
            // это новый формат отдачи
            'result' => [
                'chart_data' => $chart_data,
                'footer'     => $footer_result
            ]
        ]);
    }

    private function getDsParams($source, $result) {
        return [
            'data'                      => array_values($result),
            'label'                     => (isset($source['hint']) ? $source['hint'] : (isset($source['title']) ? $source['title'] : '')),
            'backgroundColor'           => (isset($source['style']['bg_color']) ? $source['style']['bg_color'] : 'rgba(32, 168, 216, 0.1)'),
            'borderColor'               => (isset($source['style']['border_color']) ? $source['style']['border_color'] : 'rgba(32, 168, 216)'),
            'pointHoverBackgroundColor' => '#fff',
            'borderWidth'               => 2
        ];
    }

    private function getData($source, $period) {

        $this->model->
                selectOnly($source['key'], 'date')->
                select('COUNT(1)', 'count')->
                filterFunc($source['key'], "(CURDATE() - INTERVAL {$period} {$this->interval})", '>=');

        // совместимость
        if (isset($source['filter'])) {
            $source['filters'] = $source['filter'];
            unset($source['filter']);
        }

        $this->model->applyDatasetFilters($source, true);

        if($this->interval === 'DAY'){
            $this->model->group_by = $period < 365 ? "MONTH({$source['key']}), DAY({$source['key']})" : "EXTRACT(YEAR_MONTH FROM {$source['key']})";
        } elseif($this->interval !== 'YEAR') {
            $this->model->group_by = "MONTH({$source['key']}), DAY({$source['key']})";
        } else {
            $this->model->group_by = "EXTRACT(YEAR_MONTH FROM {$source['key']})";
        }

        return $this->model->get($source['table'], false, false) ?: [];
    }

    private function getFooterData($source) {

        $this->model->applyDatasetFilters($source, true);

        return $this->model->getCount($source['table'], 'id', true);
    }

}
