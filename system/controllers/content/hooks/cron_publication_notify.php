<?php

class onContentCronPublicationNotify extends cmsAction {

	public function run(){

		$ctypes = $this->model->getContentTypes();

        $notify_items = [];

		foreach($ctypes as $ctype){

			if (!$ctype['is_date_range']) { continue; }
			if (empty($ctype['options']['notify_end_date_days'])) { continue; }
			if (empty($ctype['options']['notify_end_date_notice'])) { continue; }

            $items = $this->model->filterEqual('is_pub', 1)->
                    filterIsNull('is_deleted')->
                    filterNotNull('date_pub_end')->
                    filter('i.date_pub_end <= DATE_ADD(NOW(), INTERVAL '.$ctype['options']['notify_end_date_days'].' DAY)')->
                    get($this->model->table_prefix.$ctype['name']);

            if(!$items){
                continue;
            }

            $notify_items[$ctype['name']] = $items;

            foreach ($items as $item) {

                $notice_data = [
                    'page_url'     => href_to_abs($ctype['name'], $item['slug'].'.html'),
                    'page_title'   => $item['title'],
                    'date_pub_end' => string_date_age($item['date_pub_end'], array('d', 'h', 'i'))
                ];

                $this->controller_messages->addRecipient($item['user_id']);

                $this->controller_messages->sendNoticePM(array(
                    'content' => sprintf($ctype['options']['notify_end_date_notice'], $notice_data['date_pub_end'], $notice_data['page_url'], $notice_data['page_title']),
                    'actions' => array(
                        'view' => array(
                            'title' => LANG_SHOW,
                            'href'  => $notice_data['page_url']
                        )
                    )
                ), 'notify_expired_'.$ctype['name']);

                $this->controller_messages->sendNoticeEmail('content_date_pub_end', $notice_data, 'notify_expired_'.$ctype['name']);

                $this->controller_messages->clearRecipients();

            }

		}

        if($notify_items){
            cmsEventsManager::hook('notify_expired_content_items', $notify_items);
        }

        return true;

    }

}
