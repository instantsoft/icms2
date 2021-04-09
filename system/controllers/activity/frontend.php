<?php
/**
 * @property \modelActivity $model
 */
class activity extends cmsFrontend {

    protected $useOptions = true;

    protected $unknown_action_as_index_param = true;

    public function addType($type){
        return $this->model->addType($type);
    }

    public function updateType($controller, $name, $new_type){

        $type = $this->model->getType($controller, $name);

        return $this->model->updateType($type['id'], $new_type);

    }

    public function isTypeExists($controller, $name){
        return (bool)$this->model->getType($controller, $name);
    }

    public function deleteType($controller, $name){
        return $this->model->deleteType($controller, $name);
    }


//============================================================================//
//============================================================================//

    public function addEntry($controller, $name, $entry){

        $type = $this->model->getType($controller, $name);

        if (empty($type['is_enabled'])) { return false; }

        if (!isset($entry['user_id'])) {
            $entry['user_id'] = $this->cms_user->id;
        }

        if (!isset($entry['type_id'])) {
            $entry['type_id'] = $type['id'];
        }

        $hook_start_name = 'activity_'.$controller.'_'.str_replace('.', '_', $name);

        $entry = cmsEventsManager::hook('activity_before_add', $entry);
        if($entry === false){ return false; }
        $entry = cmsEventsManager::hook($hook_start_name.'_before_add', $entry);
        if($entry === false){ return false; }

        $entry['id'] = $this->model->addEntry($entry);

        cmsEventsManager::hook('activity_after_add', $entry);
        cmsEventsManager::hook($hook_start_name.'_after_add', $entry);

        return $entry['id'];

    }

    public function updateEntry($controller, $name, $subject_id, $entry){

        $type = $this->model->getType($controller, $name);

        list($type, $subject_id, $entry) = cmsEventsManager::hook('activity_before_update_entry', [$type, $subject_id, $entry]);

        return $this->model->updateEntry($type['id'], $subject_id, $entry);
    }

    public function deleteEntry($controller, $name, $subject_id){

        $type = $this->model->getType($controller, $name);

        list($type, $subject_id) = cmsEventsManager::hook('activity_before_delete_entry', [$type, $subject_id]);

        return $this->model->deleteEntry($type['id'], $subject_id);
    }

    public function deleteEntries($controller, $name){

        $type = $this->model->getType($controller, $name);

        $type = cmsEventsManager::hook('activity_before_delete_entries', $type);

        return $this->model->deleteEntries($type['id']);
    }

//============================================================================//
//============================================================================//

    public function renderActivityList($page_url, $dataset_name=false){

        $page = $this->request->get('page', 1);
        $perpage = (empty($this->options['limit']) ? 15 : $this->options['limit']);

        // Фильтр приватности
        if (!$dataset_name || $dataset_name == 'all'){
            $this->model->filterPrivacy();
        }

		$this->model->filterEqual('is_pub', 1);

        // Постраничный вывод
        $this->model->limitPage($page, $perpage);

        cmsEventsManager::hook('activity_list_filter', $this->model);

        // Получаем количество и список записей
        $total = $this->model->getEntriesCount();
        $items = $this->model->getEntries();

        // если запрос через URL
        if($this->request->isStandard()){
            if(!$items && $page > 1){ cmsCore::error404(); }
        }

        $items = cmsEventsManager::hook('activity_before_list', $items);

        return $this->cms_template->renderInternal($this, 'list', array(
            'filters'      => array(),
            'dataset_name' => $dataset_name,
            'page_url'     => $page_url,
            'page'         => $page,
            'perpage'      => $perpage,
            'total'        => $total,
            'items'        => $items,
            'user'         => $this->cms_user
        ));

    }

    public function getDatasets(){

        $user = $this->cms_user;

        $datasets = array();

        // Все (новые)
        $datasets['all'] = array(
            'name' => 'all',
            'title' => LANG_ACTIVITY_DS_ALL,
        );

        if ($user->is_logged){
            // Мои друзья
            $datasets['friends'] = array(
                'name' => 'friends',
                'title' => LANG_ACTIVITY_DS_FRIENDS,
                'filter' => function($model) use($user){
                    return $model->filterFriendsAndSubscribe($user->id);
                }
            );
            // Только мои
            $datasets['my'] = array(
                'name' => 'my',
                'title' => LANG_ACTIVITY_DS_MY,
                'filter' => function($model) use($user){
                    return $model->filterEqual('user_id', $user->id);
                }
            );
        }

        return cmsEventsManager::hook('activity_datasets', $datasets);

    }

}
