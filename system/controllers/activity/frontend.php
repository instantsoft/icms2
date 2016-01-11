<?php

class activity extends cmsFrontend{

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

        if (!$type['is_enabled']) { return false; }

        if (!isset($entry['user_id'])) {
            $user = cmsUser::getInstance();
            $entry['user_id'] = $user->id;
        }

        if (!isset($entry['type_id'])) {
            $entry['type_id'] = $type['id'];
        }

        return $this->model->addEntry($entry);

    }

    public function updateEntry($controller, $name, $subject_id, $entry){

        $type = $this->model->getType($controller, $name);

        return $this->model->updateEntry($type['id'], $subject_id, $entry);

    }

    public function deleteEntry($controller, $name, $subject_id){

        $type = $this->model->getType($controller, $name);

        return $this->model->deleteEntry($type['id'], $subject_id);

    }

    public function deleteEntries($controller, $name){

        $type = $this->model->getType($controller, $name);

        return $this->model->deleteEntries($type['id']);

    }


//============================================================================//
//============================================================================//

    public function renderActivityList($page_url, $dataset_name=false){

        $user = cmsUser::getInstance();
        $template = cmsTemplate::getInstance();

        $page = $this->request->get('page', 1);
        $perpage = 15;

        // Фильтр приватности
        if (!$dataset_name || $dataset_name == 'all'){
            $this->model->filterPrivacy();
        }

		$this->model->filterEqual('is_pub', 1);

        // Постраничный вывод
        $this->model->limitPage($page, $perpage);

        // Получаем количество и список записей
        $total = $this->model->getEntriesCount();
        $items = $this->model->getEntries();

        $items = cmsEventsManager::hook('activity_before_list', $items);

        return $template->renderInternal($this, 'list', array(
            'filters'      => array(),
            'dataset_name' => $dataset_name,
            'page_url'     => $page_url,
            'page'         => $page,
            'perpage'      => $perpage,
            'total'        => $total,
            'items'        => $items,
            'user'         => $user
        ));

    }

    public function getDatasets(){

        $user = cmsUser::getInstance();
        $datasets = array();

        // Все (новые)
        $datasets['all'] = array(
            'name' => 'all',
            'title' => LANG_ACTIVITY_DS_ALL,
        );

        // Мои друзья
        if ($user->is_logged){
            $datasets['friends'] = array(
                'name' => 'friends',
                'title' => LANG_ACTIVITY_DS_FRIENDS,
                'filter' => function($model){
                    $user = cmsUser::getInstance();
                    return $model->filterFriends($user->id);
                }
            );
        }

        // Только мои
        if ($user->is_logged){
            $datasets['my'] = array(
                'name' => 'my',
                'title' => LANG_ACTIVITY_DS_MY,
                'filter' => function($model){
                    $user = cmsUser::getInstance();
                    return $model->filterEqual('user_id', $user->id);
                }
            );
        }

        return $datasets;

    }

}
