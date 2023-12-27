<?php

class onContentCronPublication extends cmsAction {

    public $disallow_event_db_register = true;

    public function run() {

        $ctypes = $this->model->getContentTypes();

        $is_pub_items = [];

        foreach ($ctypes as $ctype) {

            if (!$ctype['is_date_range']) {
                continue;
            }

            $pub_items = $this->model->filterEqual('is_pub', 0)->
                    filterIsNull('is_deleted')->
                    filter('i.date_pub <= NOW()')->
                    filterStart()->
                    filter('i.date_pub_end > NOW()')->
                    filterOr()->
                    filterIsNull('i.date_pub_end')->
                    filterEnd()->
                    limit(false)->
                    get($this->model->getContentTypeTableName($ctype['name']));

            if ($pub_items) {

                $this->model->publishDelayedContentItems($ctype['name'], array_keys($pub_items));

                $is_pub_items[$ctype['name']] = $pub_items;
            }

            if ($ctype['options']['is_date_range_process'] === 'delete') {
                $this->model->deleteExpiredContentItems($ctype['name']);
            } elseif ($ctype['options']['is_date_range_process'] === 'in_basket') {
                $this->model->toTrashExpiredContentItems($ctype['name']);
            } else {
                $this->model->hideExpiredContentItems($ctype['name']);
            }
        }

        if ($is_pub_items) {
            cmsEventsManager::hook('publish_delayed_content', $is_pub_items);
        }

        return true;
    }

}
