<?php

class onGroupsUserTabShow extends cmsAction {

    public function run($profile, $tab_name, $tab){

        // устанавливаем контекст списка
        $this->setListContext('profile_content');

        // Текущий набор
        $dataset = $this->request->get('dataset', '');

        // Получаем список наборов
        $datasets = $this->getDatasets();

        // Если есть наборы, применяем фильтры текущего
        if ($datasets){

            if($dataset && empty($datasets[$dataset])){ cmsCore::error404(); }

            $keys = array_keys($datasets);
            $current_dataset = $dataset ? $datasets[$dataset] : $datasets[$keys[0]];
            $this->model->applyDatasetFilters($current_dataset);
            // устанавливаем максимальное количество записей для набора, если задано
            if(!empty($current_dataset['max_count'])){
                $this->max_items_count = $current_dataset['max_count'];
            }
            // если набор всего один, например для изменения сортировки по умолчанию,
            // не показываем его на сайте
            if(count($datasets) == 1){
                unset($current_dataset); $datasets = false;
            }

        }

        $this->model->filterByMember($profile['id']);

        $page_url = href_to_profile($profile, array('groups'));

        if ($this->cms_user->id == $profile['id'] || $this->cms_user->is_admin){
            $this->model->disableApprovedFilter();
        }

        $list_html = $this->renderGroupsList($page_url.($dataset ? '/'.$dataset : ''), 'popular');

        return $this->cms_template->renderInternal($this, 'profile_tab', array(
            'user'            => $this->cms_user,
            'tab'             => $tab,
            'profile'         => $profile,
            'datasets'        => $datasets,
            'dataset'         => $dataset,
            'current_dataset' => (isset($current_dataset) ? $current_dataset : array()),
            'base_ds_url'     => $page_url . '%s',
            'html'            => $list_html
        ));

    }

}
