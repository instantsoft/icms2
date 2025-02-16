<?php

class actionGroupsIndex extends cmsAction {

    public function run($dataset_name = false) {

        $current_dataset = [];

        $datasets = $this->getDatasets();

        // Если есть наборы, применяем фильтры текущего
        // иначе будем сортировать по дате создания
        if ($datasets) {

            if ($dataset_name && empty($datasets[$dataset_name])) {
                return cmsCore::error404();
            }

            $keys = array_keys($datasets);
            $current_dataset = $dataset_name ? $datasets[$dataset_name] : $datasets[$keys[0]];

            $this->model->applyDatasetFilters($current_dataset);

            // устанавливаем максимальное количество записей для набора, если задано
            if (!empty($current_dataset['max_count'])) {
                $this->max_items_count = $current_dataset['max_count'];
            }

            // если набор всего один, например для изменения сортировки по умолчанию,
            // не показываем его на сайте
            if (count($datasets) == 1) {
                $current_dataset = [];
                $datasets        = false;
                $dataset_name    = false;
            }
        }

        // Формируем базовые URL для страниц
        $page_url = [
            'base'  => href_to($this->name, $dataset_name ? $dataset_name : ''),
            'first' => href_to($this->name, $dataset_name ? $dataset_name : '')
        ];

        $this->cms_template->addBreadcrumb(LANG_GROUPS, href_to('groups'));

        if ($current_dataset) {

            foreach ([
                'seo_title' => 'setPageTitle',
                'seo_h1'    => 'setPageH1',
                'seo_keys'  => 'setPageKeywords',
                'seo_desc'  => 'setPageDescription'
            ] as $seo_key => $method) {
                if (!empty($current_dataset[$seo_key])) {
                    call_user_func([$this->cms_template, $method], $current_dataset[$seo_key]);
                } else {
                    call_user_func([$this->cms_template, $method . 'Item'], $current_dataset);
                }
            }
        }

        if (cmsUser::isAllowed('groups', 'add')) {
            $this->cms_template->addToolButton([
                'class' => 'add',
                'title' => LANG_GROUPS_ADD,
                'icon'  => 'plus-circle',
                'href'  => href_to('groups', 'add')
            ]);
        }

        if (cmsUser::isAdmin()) {
            $this->cms_template->addToolButton([
                'class' => 'page_gear',
                'icon'  => 'wrench',
                'title' => LANG_GROUPS_SETTINGS,
                'href'  => href_to('admin', 'controllers', ['edit', 'groups'])
            ]);
        }

        return $this->cms_template->render('index', [
            'datasets'         => $datasets,
            'base_ds_url'      => href_to_rel('groups') . '%s',
            'dataset_name'     => $dataset_name,
            'dataset'          => $current_dataset,
            'h1_title'         => LANG_GROUPS, // не используется, совместимость шаблонов
            'user'             => $this->cms_user,
            'groups_list_html' => $this->renderGroupsList($page_url, $dataset_name)
        ], $this->request);

    }

}
