<?php
/**
 * @property \modelSubscriptions $model
 */
class actionSubscriptionsSubscribe extends cmsAction {

    private $target = [];
    private $subscribe = [];

    private $need_email_confirm, $modal_close, $success_text = false;

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
        ],
        'params' => [
            'default' => [],
            'rules'   => [
                ['subscribe_params']
            ]
        ]
    ];

    public function run() {

        if (!$this->request->isAjax()) {
            //return cmsCore::error404();
        }

        $this->target['controller'] = $this->request->get('controller');
        $this->target['subject']    = $this->request->get('subject');
        $this->target['params']     = $this->request->get('params');
        $this->target['hash']       = md5(serialize($this->target));

        if (!cmsCore::isControllerExists($this->target['controller']) ||
                !cmsController::enabled($this->target['controller'])) {

            return $this->cms_template->renderJSON([
                'error' => true
            ]);
        }

        // Контроллер подписки
        $controller = cmsCore::getController($this->target['controller'], $this->request);

        if ($this->cms_user->is_logged) {

            $this->subscribe['user_id']       = $this->cms_user->id;
            $this->subscribe['confirm_token'] = string_random(32, $this->cms_user->email);

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
                $subscriber_email = null;
            }

            $subscriber_name = cmsUser::getCookie('subscriber_name', 'string', function ($cookie) {
                return trim(strip_tags($cookie));
            });

            if (!$subscriber_name) {
                $subscriber_name = null;
            }

            // если нет куки, спрашиваем данные у гостя
            if (!$subscriber_email || !$subscriber_name) {

                $this->modal_close = true;

                $form = $this->getForm('guest');

                // показываем форму гостям
                if (!$this->request->has('email')) {

                    $confirm = $this->cms_template->renderInternal($this, 'guest_form', [
                        'form'   => $form,
                        'params' => $this->target
                    ]);

                    return $this->cms_template->renderJSON([
                        'error'         => false,
                        'confirm_title' => LANG_SBSCR_GUEST_FORM_TITLE,
                        'confirm'       => $confirm
                    ]);
                }

                $guest_data = $form->parse($this->request, true);

                $errors = $form->validate($this, $guest_data);

                if ($errors) {

                    return $this->cms_template->renderJSON([
                        'errors' => $errors
                    ]);
                }

                $subscriber_email = $guest_data['email'];
                $subscriber_name  = $guest_data['name'];

                // нам нужно подтверждение по email
                $this->need_email_confirm = true;

                // если требуется подтверждение
                if (!empty($this->options['guest_email_confirmation'])) {

                    $this->subscribe['is_confirmed'] = null;
                }
            }

            cmsUser::setCookie('subscriber_email', $subscriber_email, 15768000);
            cmsUser::setCookie('subscriber_name', $subscriber_name, 15768000);

            $this->subscribe['guest_email']   = $subscriber_email;
            $this->subscribe['guest_name']    = $subscriber_name;
            $this->subscribe['confirm_token'] = string_random(32, $this->subscribe['guest_email']);
        }

        // проверяем, не подписаны ли
        if (!$this->model->isSubscribed($this->target, $this->subscribe)) {

            // пробуем получить название списка, в хуке по сути есть небольшая валидация
            $subscribe_list_title = $controller->runHook('subscribe_list_title', [$this->target, $this->subscribe], false);

            // Если по какой-то причине получили false, отдаём ошибку
            if($subscribe_list_title === false) {

                return $this->cms_template->renderJSON([
                    'error' => true
                ]);
            }

            if (is_string($subscribe_list_title)) {
                $this->target['title'] = $subscribe_list_title;
            }

            $list_url = $controller->runHook('subscribe_item_url', [$this->target], false);

            if ($list_url) {
                $this->target['subject_url'] = $list_url;
            }

            // подписываем и возвращаем id нового списка, если он ранее не был создан
            list($now_create_list_id, $sid) = $this->model->subscribe($this->target, $this->subscribe);

            cmsEventsManager::hook('subscribe', [$this->target, $this->subscribe, $now_create_list_id, $sid]);

            // уведомляем администраторов о новом списке, если заголовок не опеределён
            if ($now_create_list_id && empty($this->target['title']) && !empty($this->options['admin_email'])) {

                $admin_emails = explode(',', $this->options['admin_email']);

                foreach ($admin_emails as $admin_email) {

                    $this->controller_messages->sendEmail(trim($admin_email), 'subscribe_new_list', [
                        'admin_slist_url' => href_to_abs('admin', 'controllers', ['edit', 'subscriptions', 'list'])
                    ]);
                }
            }

        } else {

            // если уже подписан на такой email, подтверждения подписки не нужно
            if ($this->need_email_confirm) {

                $this->need_email_confirm = false;

                $this->success_text = LANG_SBSCR_GUEST_IS_EXISTS;
            }
        }

        $this->sendConfirmEmail();

        $list_item = $this->model->getSubscriptionItem($this->target['hash']);

        return $this->cms_template->renderJSON([
            'errors'       => false,
            'error'        => false,
            'callback'     => 'successSubscribe',
            'is_subscribe' => 1,
            'count'        => $list_item['subscribers_count'],
            'modal_close'  => $this->modal_close,
            'success_text' => $this->success_text
        ]);
    }

    private function sendConfirmEmail() {

        // если требуется подтверждение
        if (!empty($this->options['guest_email_confirmation']) && $this->need_email_confirm) {

            $this->success_text = LANG_SBSCR_GUEST_EMAIL_CONFIRM_SEND;

            $to     = ['email' => $this->subscribe['guest_email'], 'name' => $this->subscribe['guest_name']];
            $letter = ['name' => 'subscriptions_guest_confirm'];

            $this->controller_messages->sendEmail($to, $letter, [
                'nickname'      => $this->subscribe['guest_name'],
                'page_url'      => href_to_abs('subscriptions', 'guest_confirm', $this->subscribe['confirm_token']),
                'confirm_token' => $this->subscribe['confirm_token'],
                'valid_until'   => html_date(date('d.m.Y H:i', time() + ($this->options['verify_exp'] * 3600)), true)
            ]);
        }

    }

}
