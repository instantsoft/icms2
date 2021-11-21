<?php

class backendRss extends cmsBackend {

    public function loadCallback() {

        $this->callbacks = [
            'actiontoggle_rss_feeds_is_enabled' => [
                function ($controller, $item) {

                    $controller_name = $item['ctype_name'];

                    if ($controller->model->isCtypeFeed($item['ctype_name'])) {
                        $controller_name = 'content';
                    }

                    cmsEventsManager::hook('rss_' . $controller_name . '_controller_after_update', $item);
                }
            ]
        ];

    }

}
