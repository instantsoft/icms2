<?php

class onSubscriptionsPublishDelayedContent extends cmsAction {

    public function run($data) {

        foreach ($data as $ctype_name => $items) {

            $result = [];

            foreach ($items as $item) {

                if (!empty($item['is_private'])) {
                    continue;
                }

                $result[] = $item;
            }

            if ($result) {

                cmsQueue::pushOn('subscriptions', [
                    'controller' => $this->name,
                    'hook'       => 'send_letters',
                    'params'     => [
                        'content', $ctype_name, $result
                    ]
                ]);
            }
        }

        return $data;
    }

}
