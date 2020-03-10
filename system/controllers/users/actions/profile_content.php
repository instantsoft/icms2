<?php

class actionUsersProfileContent extends cmsAction {

    public $lock_explicit_call = true;

    public function run($profile, $ctype_name = false, $folder_id = false, $dataset = false){

        if (!$ctype_name) { return cmsCore::error404(); }

        $ctype = $this->controller_content->model->getContentTypeByName($ctype_name);
        if (!$ctype) { return cmsCore::error404(); }

        if (!$ctype['options']['profile_on']) { return cmsCore::error404(); }

        if (!$this->cms_user->isPrivacyAllowed($profile, 'view_user_'.$ctype['name'])){
            return cmsCore::error404();
        }

        $original_folder_id = $folder_id;
        if($folder_id && !$dataset && !is_numeric($folder_id)){
            $dataset   = $folder_id;
            $folder_id = false;
        }

        $this->controller_content->setListContext('profile_content');

        // Получаем список наборов
        $datasets = $this->controller_content->getCtypeDatasets($ctype, array(
            'cat_id' => 0
        ));

        $folders = array();

        if ($ctype['is_folders']){

            $folders = $this->controller_content->model->getContentFolders($ctype['id'], $profile['id']);

            if ($folders){
                if ($folder_id && array_key_exists($folder_id, $folders)){
                    $this->controller_content->model->filterEqual('folder_id', $folder_id);
                }
            }

        }

        $this->controller_content->model->filterEqual('user_id', $profile['id']);

        list($folders, $this->controller_content->model, $profile, $original_folder_id) = cmsEventsManager::hook("user_content_{$ctype['name']}_folders", array(
            $folders,
            $this->controller_content->model,
            $profile,
            $original_folder_id
        ));

        // Если есть наборы, применяем фильтры текущего
        $current_dataset = array();
        if ($datasets){

            $keys = array_keys($datasets);

            if(!$dataset){
                $dataset = $keys[0];
            }

            if($dataset && !empty($datasets[$dataset])){


                $current_dataset = $datasets[$dataset];
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

            } else {

                if($dataset && $folder_id === false && $original_folder_id === false){
                    return cmsCore::error404();
                }

            }

        }

        if ($folders){
            $folders = array('0' => array('id' => '0', 'title' => LANG_ALL)) + $folders;
        }

        if ($this->cms_user->id != $profile['id'] && !$this->cms_user->is_admin){
            $this->controller_content->model->enableHiddenParentsFilter();
        }

        if ($this->cms_user->id == $profile['id'] || $this->cms_user->is_admin){
            $this->controller_content->model->disableApprovedFilter()->joinModerationsTasks($ctype['name']);
			$this->controller_content->model->disablePubFilter();
			$this->controller_content->model->disablePrivacyFilter();
        }

        list($ctype, $profile) = cmsEventsManager::hook('content_before_profile', array($ctype, $profile));

        if ($folder_id){
            $page_url = href_to_profile($profile, array('content', $ctype_name, $folder_id));
        } else {
            $page_url = href_to_profile($profile, array('content', $ctype_name));
        }

        // кешируем
        cmsModel::cacheResult('current_ctype', $ctype);
        cmsModel::cacheResult('current_ctype_dataset', $current_dataset);

        $list_html = $this->controller_content->renderItemsList($ctype, $page_url.($dataset ? '/'.$dataset : ''), false, 0, [], $dataset);

        $list_header = empty($ctype['labels']['profile']) ? $ctype['title'] : $ctype['labels']['profile'];

        if($current_dataset && $dataset){
            $list_header .= ' / '.$current_dataset['title'];
        }

        $toolbar_html = cmsEventsManager::hookAll('content_toolbar_html', array($ctype['name'], array(), $current_dataset, array(
            array(
                'field'     => 'user_id',
                'condition' => 'eq',
                'value'     => $profile['id']
            ),
            array(
                'field'     => 'folder_id',
                'condition' => 'eq',
                'value'     => $folder_id
            )
        )));

        return $this->cms_template->render('profile_content', array(
            'filter_titles'   => $this->controller_content->getFilterTitles(),
            'user'            => $this->cms_user,
            'toolbar_html'    => $toolbar_html,
            'id'              => $profile['id'],
            'profile'         => $profile,
            'ctype'           => $ctype,
            'folders'         => $folders,
            'folder_id'       => $original_folder_id,
            'datasets'        => $datasets,
            'dataset'         => $dataset,
            'current_dataset' => $current_dataset,
            'base_ds_url'     => $page_url . '%s',
            'list_header'     => $list_header,
            'html'            => $list_html
        ));

    }

}
