<?php

class actionAdminIndexChartData extends cmsAction {

    public function run(){

        if (!$this->request->isAjax()) { cmsCore::error404(); }

        $id      = $this->request->get('id', '');
        $section = $this->request->get('section', '');
        $period  = $this->request->get('period', '');

        if (!$id || !$section || !is_numeric($period)) { cmsCore::error404(); }

        $chart_nav = cmsEventsManager::hookAll('admin_dashboard_chart');

        $sources = $old_result = $chart_data = [];

        foreach($chart_nav as $ctrl){
            if ($ctrl['id'] == $id){
                foreach ($ctrl['sections'] as $key => $s) {
                    if($key == $section || strpos($key, $section.':') === 0){
                        $sources[] = $ctrl['sections'][$key];
                    }
                }
            }
        }

        if (!$sources) { cmsCore::error404(); }

        foreach ($sources as $source) {

            $result = $data_formatted = [];

            $data = $this->getData($source, $period);

            if ($period < 300){

                foreach($data as $item){
                    $data_formatted[date('d.m', strtotime($item['date']))] = intval($item['count']);
                }

                for($d=0; $d <= $period; $d++){
                    $date = date('d.m', strtotime("-{$d} days"));
                    $result[$date] = isset($data_formatted[$date]) ? $data_formatted[$date] : 0;
                }

            } else {

                foreach($data as $item){
                    $data_formatted[date('m.Y', strtotime($item['date']))] = intval($item['count']);
                }

                for($m=0; $m <= 12; $m++){
                    $date = date('m.Y', strtotime("-{$m} months"));
                    $result[$date] = isset($data_formatted[$date]) ? $data_formatted[$date] : 0;
                }

            }

            $result = array_reverse($result);

            // совместимость
            // теперь можно отдавать данные
            // для нескольких графиков
            if(!$old_result){
                $old_result = $result;
            }

            if(!$chart_data){
                $chart_data = [
                    'labels' => array_keys($result),
                    'datasets' => [
                        $this->getDsParams($source, $result)
                    ]
                ];
            } else {
                $chart_data['datasets'][] = $this->getDsParams($source, $result);
            }

        }

        $footer_data = $footer_result = [];

        foreach($chart_nav as $ctrl){
            if ($ctrl['id'] == $id && isset($ctrl['footer'][$section])){
                $footer_data = $ctrl['footer'][$section];
                break;
            }
        }

        if($footer_data){
            foreach ($footer_data as $fdata) {
                $footer_result[] = [
                    'count' => $this->getFooterData($fdata),
                    'title' => $fdata['title'],
                    'progress' => $fdata['progress']
                ];
            }
        }

        $this->cms_template->renderJSON(array(
            // это совместимость
            'labels' => array_keys($old_result),
            'values' => array_values($old_result),
            // это новый формат отдачи
            'result' => [
                'chart_data' => $chart_data,
                'footer'     => $footer_result
            ]
        ));

    }

    private function getDsParams($source, $result) {
        return [
            'data' => array_values($result),
            'label' => (isset($source['title']) ? $source['title'] : (isset($source['hint']) ? $source['hint'] : '')),
            'backgroundColor' => (isset($source['style']['bg_color']) ? $source['style']['bg_color'] : 'rgba(32, 168, 216, 0.1)'),
            'borderColor' => (isset($source['style']['border_color']) ? $source['style']['border_color'] : 'rgba(32, 168, 216)'),
            'pointHoverBackgroundColor' => '#fff',
            'borderWidth' => 2
        ];
    }

    private function getData($source, $period){

        $this->model->
                selectOnly($source['key'], 'date')->
                select('COUNT(1)', 'count')->
                filterFunc($source['key'], "(CURDATE() - INTERVAL {$period} DAY)", '>=')->
                orderBy($source['key'], 'asc');

        // совместимость
        if(isset($source['filter'])){
            $source['filters'] = $source['filter']; unset($source['filter']);
        }

        $this->model->applyDatasetFilters($source, true);

        $this->model->group_by = $period < 300 ? "DAY({$source['key']})" : "MONTH({$source['key']})";

        return (array)$this->model->get($source['table'], false, false);

    }

    private function getFooterData($source){

        $this->model->applyDatasetFilters($source, true);

        return $this->model->getCount($source['table'], 'id', true);

    }

}
