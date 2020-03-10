<?php

class actionGroupsGroupContent extends cmsAction {

    public $lock_explicit_call = true;

    public function run($group, $ctype_name = false, $dataset = false){

        if (!$ctype_name) { cmsCore::error404(); }

        $ctype = $this->controller_content->model->getContentTypeByName($ctype_name);
        if (!$ctype || empty($ctype['is_in_groups'])) { cmsCore::error404(); }

        $this->controller_content->setListContext('group_content');

        // Получаем список наборов
        $datasets = $this->controller_content->getCtypeDatasets($ctype, array(
            'cat_id' => 0
        ));

        // Если есть наборы, применяем фильтры текущего
        $current_dataset = array();
        if ($datasets){

            if($dataset && empty($datasets[$dataset])){ cmsCore::error404(); }

            $keys = array_keys($datasets);
            $current_dataset = $dataset ? $datasets[$dataset] : $datasets[$keys[0]];

            $this->controller_content->model->applyDatasetFilters($current_dataset);
            // устанавливаем максимальное количество записей для набора, если задано
            if(!empty($current_dataset['max_count'])){
                $this->controller_content->max_items_count = $current_dataset['max_count'];
            }
            // если набор всего один, например для изменения сортировки по умолчанию,
            // не показываем его на сайте
            if(count($datasets) == 1){
                $current_dataset = array(); $datasets = false;
            }

        }

        $this->controller_content->model->
                filterEqual('parent_id', $group['id'])->
                filterEqual('parent_type', 'group');

        $page_url = href_to($this->name, $group['slug'], array('content', $ctype_name));

        if (($this->cms_user->id == $group['owner_id']) || $this->cms_user->is_admin){
            $this->controller_content->model->disableApprovedFilter()->joinModerationsTasks($ctype['name']);
			$this->controller_content->model->disablePubFilter();
            $this->controller_content->model->disablePrivacyFilter();
        }

        $this->filterPrivacyGroupsContent($ctype, $this->controller_content->model, $group);

        $group['sub_title'] = empty($ctype['labels']['profile']) ? $ctype['title'] : $ctype['labels']['profile'];

        if($current_dataset && $dataset){
            $group['sub_title'] .= ' / '.$current_dataset['title'];
        }

        // кешируем
        cmsModel::cacheResult('current_ctype', $ctype);
        cmsModel::cacheResult('current_ctype_dataset', $current_dataset);

        $toolbar_html = cmsEventsManager::hookAll('content_toolbar_html', array($ctype['name'], array(), $current_dataset, array(
            array(
                'field'     => 'parent_id',
                'condition' => 'eq',
                'value'     => $group['id']
            ),
            array(
                'field'     => 'parent_type',
                'condition' => 'eq',
                'value'     => 'group'
            )
        )));

        $this->cms_template->addBreadcrumb(LANG_GROUPS, href_to('groups'));
        $this->cms_template->addBreadcrumb($group['title'], href_to('groups', $group['slug']));
        $this->cms_template->addBreadcrumb($group['sub_title']);

        return $this->cms_template->render('group_content', array(
            'user'            => $this->cms_user,
            'toolbar_html'    => $toolbar_html,
            'group'           => $group,
            'ctype'           => $ctype,
            'datasets'        => $datasets,
            'dataset'         => $dataset,
            'current_dataset' => $current_dataset,
            'base_ds_url'     => $page_url . '%s',
            'html'            => $this->controller_content->renderItemsList($ctype, $page_url.($dataset ? '/'.$dataset : ''), false, 0, [], $dataset),
            'filter_titles'   => $this->controller_content->getFilterTitles(),
        ));

    }

}
