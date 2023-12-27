<?php

class onContentCronPublicationNotify extends cmsAction {

    public $disallow_event_db_register = true;

    public function run() {

        $ctypes = $this->model->getContentTypes();

        $notify_items = [];

        foreach ($ctypes as $ctype) {

            if (!$ctype['is_date_range']) {
                continue;
            }
            if (empty($ctype['options']['notify_end_date_days'])) {
                continue;
            }
            if (empty($ctype['options']['notify_end_date_notice'])) {
                continue;
            }

            $items = $this->model->filterGtEqual('is_pub', 1)->
                    filterIsNull('is_deleted')->
                    filterNotNull('date_pub_end')->
                    filter('i.date_pub_end <= DATE_ADD(NOW(), INTERVAL ' . $ctype['options']['notify_end_date_days'] . ' DAY)')->
                    get($this->model->getContentTypeTableName($ctype['name']));

            if (!$items) {
                continue;
            }

            $notify_items[$ctype['name']] = $items;

            foreach ($items as $item) {

                $ups_key = 'notify_expired_'.$ctype['name'].'_'.$item['id'];

                $is_send = cmsUser::getUPS($ups_key, $item['user_id']);

                if ($is_send) {
                    continue;
                }

                cmsUser::setUPS($ups_key, 1, $item['user_id']);

                $notice_data = [
                    'page_url'     => href_to_abs($ctype['name'], $item['slug'] . '.html'),
                    'page_title'   => $item['title'],
                    'date_pub_end' => string_date_age($item['date_pub_end'], ['d', 'h', 'i'])
                ];

                $this->controller_messages->addRecipient($item['user_id']);

                $this->controller_messages->sendNoticePM([
                    'content' => sprintf($ctype['options']['notify_end_date_notice'], $notice_data['date_pub_end'], $notice_data['page_url'], $notice_data['page_title']),
                    'actions' => [
                        'view' => [
                            'title' => LANG_SHOW,
                            'href'  => $notice_data['page_url']
                        ]
                    ]
                ], 'notify_expired_' . $ctype['name']);

                $this->controller_messages->sendNoticeEmail('content_date_pub_end', $notice_data, 'notify_expired_' . $ctype['name']);

                $this->controller_messages->clearRecipients();
            }
        }

        if ($notify_items) {
            cmsEventsManager::hook('notify_expired_content_items', $notify_items);
        }

        return true;
    }

}
