<?php

class modelAdmin extends cmsModel {

    use icms\traits\controllers\models\transactable;

//============================================================================//
//==========================    КОМПОНЕНТЫ   =================================//
//============================================================================//

    public function getInstalledControllers() {

        if (!$this->order_by) {
            $this->orderByList([
                [
                    'by' => 'is_enabled',
                    'to' => 'desc'
                ],
                [
                    'by' => 'title',
                    'to' => 'asc'
                ]
            ]);
        }

        return $this->get('controllers', function ($item, $model) {

            $item['options'] = cmsModel::yamlToArray($item['options']);

            $item['title'] = string_lang($item['name'] . '_CONTROLLER', $item['title']);

            return $item;
        }) ?: [];
    }

    public function getControllerInfo($controller_name){

        return $this->getItemByField('controllers', 'name', $controller_name, function($item){
            $item['options'] = cmsModel::yamlToArray($item['options']);
            $item['files'] = cmsModel::yamlToArray($item['files']);
            $item['title'] = string_lang($item['name'].'_CONTROLLER', $item['title']);
            return $item;
        });
    }

//============================================================================//
//===========================    Дополнения    ===============================//
//============================================================================//

    public function getInstalledAddonsIds() {

        $controllers_addons = $this->selectOnly('addon_id')->
            filterNotNull('addon_id')->
            get('controllers', function ($item, $model) {
                return $item['addon_id'];
            }, false) ?: [];

        $widgets_addons = $this->selectOnly('addon_id')->
            filterNotNull('addon_id')->
            get('widgets', function ($item, $model) {
                return $item['addon_id'];
            }, false) ?: [];

        return array_filter(array_merge($widgets_addons, $controllers_addons));
    }

//============================================================================//
//============================    События    =================================//
//============================================================================//

    public function getEvents() {

        $this->limit = false;

        return $this->get('events');
    }

    public function reorderEvents($ids_list) {

        $this->reorderByList('events', $ids_list);

        cmsCache::getInstance()->clean('events');

        return true;
    }

    public function addEvent($listener, $event) {

        $id = $this->insert('events', [
            'listener' => $listener,
            'event'    => $event,
            'ordering' => $this->getNextOrdering('events')
        ]);

        cmsCache::getInstance()->clean('events');

        return $id;
    }

    public function deleteEvent($listener, $event) {

        $this->filterEqual('listener', $listener);
        $this->filterEqual('event', $event);
        $this->deleteFiltered('events');

        cmsCache::getInstance()->clean('events');

        return true;
    }

//============================================================================//
//==========================    ПЛАНИРОВЩИК    ===============================//
//============================================================================//

    public function getPendingSchedulerTasks() {

        $tasks = $this->filterEqual('is_active', 1)->
                orderBy('ordering', 'asc')->
                get('scheduler_tasks');

        $pending = [];

        if ($tasks) {
            foreach ($tasks as $task) {

                if ($task['is_new']) {
                    $pending[] = $task;
                    continue;
                }

                $time_last_run = strtotime($task['date_last_run']);
                $time_now      = time();

                $minutes_ago = floor(($time_now - $time_last_run) / 60);

                if ($minutes_ago >= $task['period']) {
                    $pending[] = $task;
                    continue;
                }
            }
        }

        return $pending;
    }

    public function getSchedulerTask($id) {

        return $this->getItemById('scheduler_tasks', $id);
    }

    public function addSchedulerTask($task) {

        return $this->insert('scheduler_tasks', $task);
    }

    public function updateSchedulerTask($id, $task) {

        return $this->update('scheduler_tasks', $id, $task);
    }

    public function updateSchedulerTaskDate($task) {

        return $this->updateSchedulerTask($task['id'], [
            'is_new'        => 0,
            'date_last_run' => ($task['is_strict_period'] ? date('Y-m-d H:i:s', (strtotime($task['date_last_run']) + ($task['period'] * 60))) : null)
        ]);
    }

    public function toggleSchedulerPublication($id, $is_active) {

        return $this->update('scheduler_tasks', $id, [
            'is_active' => $is_active
        ]);
    }

    public function getTableItemsCount24($table_name, $date_pub_field = 'date_pub') {

        $this->filterDateYounger($date_pub_field, 1);

        return $this->getCount($table_name, 'id', true);
    }

}
