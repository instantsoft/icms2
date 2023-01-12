<?php

class backendRss extends cmsBackend {

    public function __construct(cmsRequest $request) {

        parent::__construct($request);

        $this->addEventListener('actiontoggle_rss_feeds_is_enabled', function ($controller, $item) {

            $controller_name = $item['ctype_name'];

            if ($controller->model->isCtypeFeed($item['ctype_name'])) {
                $controller_name = 'content';
            }

            cmsEventsManager::hook('rss_' . $controller_name . '_controller_after_update', $item);
        });
    }

}
