<?php

class actionActivityIndex extends cmsAction{

    public function run($dataset_name = ''){

        $datasets = $this->getDatasets();

        if(!isset($datasets[$dataset_name])){
            return cmsCore::error404();
        }

        $dataset = $datasets[$dataset_name];

        if (isset($dataset['filter']) && is_callable($dataset['filter'])){
            $this->model = $dataset['filter']( $this->model );
        }

        // Формируем базовые URL для страниц
        $page_url = !$dataset_name ? href_to($this->name) : href_to($this->name, $dataset_name);

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

        $this->cms_template->addHead('<link rel="canonical" href="'.href_to_abs($this->name).'">');

        // В контроллере используется свойство useSeoOptions,
        // Поэтому тайтл уже задан. Дополняем набором
        if ($dataset_name) {
            $this->cms_template->addToPageTitle($dataset['title']);
        }

        return $this->cms_template->render('index', [
            'page_title'      => '', // Не используется, совместимость шаблонов
            'base_ds_url'     => href_to($this->name).'%s',
            'datasets'        => $datasets,
            'dataset_name'    => $dataset_name,
            'dataset'         => $dataset,
            'user'            => $this->cms_user,
            'items_list_html' => $items_list_html
        ], $this->request);
    }

}
