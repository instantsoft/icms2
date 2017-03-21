<?php

class actionActivityIndex extends cmsAction{

    public function run($tab='all'){

        $dataset_name = false;
        $datasets = $this->getDatasets();

        if ($tab && isset($datasets[$tab])) {

            $dataset_name = $tab;
            $dataset = $datasets[$tab];

            if (isset($dataset['filter']) && is_callable($dataset['filter'])){
                $this->model = $dataset['filter']( $this->model );
            }

        } else if ($tab) { cmsCore::error404(); }

        // Формируем базовые URL для страниц
        $page_url = array(
            'base'  => href_to($this->name, $dataset_name ? 'index/'.$dataset_name : ''),
            'first' => href_to($this->name, $dataset_name ? 'index/'.$dataset_name : '')
        );

        $this->model->filterHiddenParents();

        // Получаем HTML списка записей
        $items_list_html = $this->renderActivityList($page_url, $dataset_name);

        return $this->cms_template->render('index', array(
            'datasets'        => $datasets,
            'dataset_name'    => $dataset_name,
            'dataset'         => $dataset,
            'user'            => $this->cms_user,
            'items_list_html' => $items_list_html
        ), $this->request);

    }

}
