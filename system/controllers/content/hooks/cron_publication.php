<?php

class onContentCronPublication extends cmsAction {

	public function run(){

		$ctypes = $this->model->getContentTypes();

        $is_pub_items = array();

		foreach($ctypes as $ctype){

			if (!$ctype['is_date_range']) { continue; }

            $pub_items = $this->model->filterNotEqual('is_pub', 1)->
                    filterIsNull('is_deleted')->
                    filter('i.date_pub <= NOW()')->
                    filterStart()->
                        filter('i.date_pub_end > NOW()')->
                        filterOr()->
                        filterIsNull('i.date_pub_end')->
                    filterEnd()->
                    get($this->model->table_prefix.$ctype['name']);

            if($pub_items){
                $this->model->publishDelayedContentItems($ctype['name']);
                $is_pub_items[$ctype['name']] = $pub_items;
            }

            if($ctype['options']['is_date_range_process'] === 'delete') {
                $this->model->deleteExpiredContentItems($ctype['name']);
            } elseif($ctype['options']['is_date_range_process'] === 'in_basket') {
                $this->model->toTrashExpiredContentItems($ctype['name']);
            } else {
                $this->model->hideExpiredContentItems($ctype['name']);
            }

		}

        if($is_pub_items){
            cmsEventsManager::hook('publish_delayed_content', $is_pub_items);
        }

    }

}
