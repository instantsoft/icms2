<?php

class actionActivityIndex extends cmsAction{

    public function run($dataset_name = 'all'){

        $datasets = $this->getDatasets();

        if(!$dataset_name || !isset($datasets[$dataset_name])){
            cmsCore::error404();
        }

        $dataset = $datasets[$dataset_name];

        if (isset($dataset['filter']) && is_callable($dataset['filter'])){
            $this->model = $dataset['filter']( $this->model );
        }

        // Формируем базовые URL для страниц
        $page_url = $dataset_name === 'all' ? href_to($this->name) : href_to($this->name, $dataset_name);

        $this->model->enableHiddenParentsFilter();

        // Получаем HTML списка записей
        $items_list_html = $this->renderActivityList($page_url, $dataset_name);

        if ($this->cms_user->is_admin){
            $this->cms_template->addToolButton([
                'class' => 'page_gear',
                'icon'  => 'wrench',
                'title' => LANG_OPTIONS,
                'href'  => href_to('admin', 'controllers', ['edit', $this->name, 'options'])
            ]);
        }

        $this->cms_template->addHead('<link rel="canonical" href="'.href_to_abs($this->name).'"/>');

        return $this->cms_template->render('index', [
            'page_title'      => ($dataset_name != 'all' ? LANG_ACTIVITY . ' - ' . $dataset['title'] : LANG_ACTIVITY),
            'base_ds_url'     => href_to($this->name).'%s',
            'datasets'        => $datasets,
            'dataset_name'    => $dataset_name,
            'dataset'         => $dataset,
            'user'            => $this->cms_user,
            'items_list_html' => $items_list_html
        ], $this->request);
    }

}
