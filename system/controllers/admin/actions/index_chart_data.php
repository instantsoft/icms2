<?php

class actionAdminIndexChartData extends cmsAction {

    public function run(){

        if (!$this->request->isAjax()) { cmsCore::error404(); }

        $id      = $this->request->get('id', '');
        $section = $this->request->get('section', '');
        $period  = $this->request->get('period', '');

        if (!$id || !$section || !is_numeric($period)) { cmsCore::error404(); }

        $chart_nav = cmsEventsManager::hookAll('admin_dashboard_chart');

        $source = array();

        foreach($chart_nav as $ctrl){
            if ($ctrl['id'] == $id && isset($ctrl['sections'][$section])){
                $source = $ctrl['sections'][$section];
            }
        }

        if (!$source) { cmsCore::error404(); }

        $data = $this->getData($source, $period);

        $data_formatted = $result = array();

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

        $this->cms_template->renderJSON(array(
            'labels' => array_keys($result),
            'values' => array_values($result)
        ));

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

}
