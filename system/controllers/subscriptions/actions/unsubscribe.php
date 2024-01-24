<?php
/**
 * @property \modelSubscriptions $model
 */
class actionSubscriptionsUnsubscribe extends cmsAction {

    private $target    = [];
    private $subscribe = [];

    public $request_params = [
        'controller' => [
            'default' => '',
            'rules'   => [
                ['required'],
                ['sysname']
            ]
        ],
        'subject' => [
            'default' => '',
            'rules'   => [
                ['required'],
                ['sysname']
            ]
        ]
    ];

    public function run() {

        if (!$this->request->isAjax()) {
            return cmsCore::error404();
        }

        $this->target['controller'] = $this->request->get('controller');
        $this->target['subject']    = $this->request->get('subject');
        $this->target['params']     = $this->request->get('params', []);
        $this->target['hash']       = md5(serialize($this->target));

        if (!cmsCore::isControllerExists($this->target['controller']) ||
                !cmsController::enabled($this->target['controller'])) {

            return $this->cms_template->renderJSON([
                'error' => true
            ]);
        }

        $list_item = $this->model->getSubscriptionItem($this->target['hash']);

        if (!$list_item) {

            return $this->cms_template->renderJSON([
                'error' => true
            ]);
        }

        if ($this->cms_user->is_logged) {

            $this->subscribe['user_id'] = $this->cms_user->id;

            $this->subscribe['id'] = $this->model->isUserSubscribed($this->cms_user->id, $list_item['id']);

        } else {

            // если подписка разрешена только авторизованным
            if (!empty($this->options['need_auth'])) {

                return $this->cms_template->renderJSON([
                    'error'         => false,
                    'confirm_title' => LANG_AUTHORIZATION,
                    'confirm_url'   => href_to('auth', 'login')
                ]);
            }

            // ищем куки гостя
            $subscriber_email = cmsUser::getCookie('subscriber_email', 'string', function ($cookie) {
                return trim($cookie);
            });

            if (!$subscriber_email || $this->validate_email($subscriber_email) !== true) {

                return $this->cms_template->renderJSON([
                    'error' => true
                ]);
            }

            $this->subscribe['guest_email'] = $subscriber_email;

            $this->subscribe['id'] = $this->model->isGuestSubscribed($subscriber_email, $list_item['id']);
        }

        $this->model->unsubscribe($this->target, $this->subscribe);

        cmsEventsManager::hook('unsubscribe', [$this->target, $this->subscribe]);

        return $this->cms_template->renderJSON([
            'errors'       => false,
            'error'        => false,
            'callback'     => 'successSubscribe',
            'is_subscribe' => 0,
            'count'        => ($list_item['subscribers_count'] - 1)
        ]);
    }

}
