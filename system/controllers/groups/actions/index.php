<?php

class actionGroupsIndex extends cmsAction {

    public function run($dataset_name = false){

        $current_dataset = array();
        $datasets = $this->getDatasets();

        // Если есть наборы, применяем фильтры текущего
        // иначе будем сортировать по дате создания
        if ($datasets){
            if($dataset_name && empty($datasets[$dataset_name])){ cmsCore::error404(); }
            $keys = array_keys($datasets);
            $current_dataset = $dataset_name ? $datasets[$dataset_name] : $datasets[$keys[0]];
            $this->model->applyDatasetFilters($current_dataset);
            // устанавливаем максимальное количество записей для набора, если задано
            if(!empty($current_dataset['max_count'])){
                $this->max_items_count = $current_dataset['max_count'];
            }
            // если набор всего один, например для изменения сортировки по умолчанию,
            // не показываем его на сайте
            if(count($datasets) == 1){
                $current_dataset = array(); $datasets = false; $dataset_name = false;
            }
        }

        // Формируем базовые URL для страниц
        $page_url = array(
            'base'  => href_to($this->name, $dataset_name ? $dataset_name : ''),
            'first' => href_to($this->name, $dataset_name ? $dataset_name : '')
        );

        $this->cms_template->setPageTitle(LANG_GROUPS);
        $this->cms_template->addBreadcrumb(LANG_GROUPS, href_to('groups'));

        $h1_title = LANG_GROUPS;

        if($current_dataset){

            $this->cms_template->setPageTitle(!empty($current_dataset['seo_title']) ? $current_dataset['seo_title'] : $current_dataset['title']);

            if (!empty($current_dataset['seo_keys'])){
                $this->cms_template->setPageKeywords($current_dataset['seo_keys']);
            }

            if (!empty($current_dataset['seo_desc'])){
                $this->cms_template->setPageDescription($current_dataset['seo_desc']);
            }

        }

        if (cmsUser::isAllowed('groups', 'add')) {
            $this->cms_template->addToolButton(array(
                'class' => 'add',
                'title' => LANG_GROUPS_ADD,
                'icon'  => 'plus-circle',
                'href'  => href_to('groups', 'add'),
            ));
        }

        if (cmsUser::isAdmin()){
            $this->cms_template->addToolButton(array(
                'class' => 'page_gear',
                'icon'  => 'wrench',
                'title' => LANG_GROUPS_SETTINGS,
                'href'  => href_to('admin', 'controllers', array('edit', 'groups'))
            ));
        }

        return $this->cms_template->render('index', array(
            'datasets'         => $datasets,
            'base_ds_url'      => href_to_rel('groups') . '%s',
            'dataset_name'     => $dataset_name,
            'dataset'          => $current_dataset,
            'h1_title'         => $h1_title,
            'user'             => $this->cms_user,
            'groups_list_html' => $this->renderGroupsList($page_url, $dataset_name)
        ), $this->request);

    }

}
