<?php
/**
 * @property \modelComments $model
 */
class actionCommentsSubmit extends cmsAction {

    /**
     * @var array Описание правил валидации входных данных
     */
    public $request_params = [
        'tc' => [
            'default' => '',
            'rules'   => [
                ['required'],
                ['sysname'],
                ['max_length', 32]
            ]
        ],
        'ts' => [
            'default' => '',
            'rules'   => [
                ['required'],
                ['sysname'],
                ['max_length', 32]
            ]
        ],
        'ti' => [
            'default' => 0,
            'rules'   => [
                ['required'],
                ['digits']
            ]
        ],
        'parent_id' => [
            'default' => 0,
            'rules'   => [
                ['digits']
            ]
        ],
        'tud' => [
            'default' => 0,
            'rules'   => [
                ['digits']
            ]
        ],
        'id' => [
            'default' => 0,
            'rules'   => [
                ['digits']
            ]
        ],
        'author_email' => [
            'default' => '',
            'rules'   => [
                ['email'],
                ['max_length', 100]
            ]
        ],
        'author_name' => [
            'default' => '',
            'rules'   => [
                ['localealphanumeric'],
                ['max_length', 100]
            ]
        ],
        'action' => [
            'default' => '',
            'rules'   => [
                ['required'],
                ['array_key', ['add' => 'add', 'preview' => 'preview', 'update' => 'update']]
            ]
        ]
    ];

    public function run() {

        if (!$this->request->isAjax()) {
            return cmsCore::error404();
        }

        // Проверяем CSRF токен
        if (!cmsForm::validateCSRFToken($this->request->get('csrf_token', ''))) {

            return $this->cms_template->renderJSON([
                'error'   => true,
                'message' => LANG_COMMENT_ERROR
            ]);
        }

        // параметры комментария
        $this->target_controller = $this->request->get('tc');
        $this->target_subject    = $this->request->get('ts');
        $this->target_id         = $this->request->get('ti');
        $this->target_user_id    = $this->request->get('tud');
        $this->parent_id         = $this->request->get('parent_id');
        $this->comment_id        = $this->request->get('id');
        $this->content           = $this->request->get('content', '');
        $this->author_name       = $this->request->get('author_name');
        $this->author_email      = $this->request->get('author_email');

        // Проверяем наличие контроллера и модели
        if (!(cmsCore::isControllerExists($this->target_controller) &&
                cmsCore::isModelExists($this->target_controller) &&
                cmsController::enabled($this->target_controller))) {

            return $this->cms_template->renderJSON([
                'error'   => true,
                'message' => LANG_COMMENT_ERROR
            ]);
        }

        $typograph_id = $this->options['typograph_id'] ?? 1;

        $editor_params = cmsCore::getController('wysiwygs')->getEditorParams([
            'editor'  => $this->options['editor'],
            'presets' => $this->options['editor_presets']
        ]);

        // Типографируем текст для вывода
        $this->content_html = cmsEventsManager::hook('html_filter', [
            'text'         => $this->content,
            'typograph_id' => $typograph_id,
            'is_auto_br'   => !$editor_params['editor'] ? true : null
        ]);

        // Типографируем исходный текст без колбэков
        $this->content = cmsEventsManager::hook('html_filter', [
            'text'         => $this->content,
            'is_process_callback' => false,
            'typograph_id' => $typograph_id,
            'is_auto_br'   => false
        ]);

        // Если редактор не указан, то это textarea, вырезаем все теги
        if (!$editor_params['editor']) {

            $this->content_html = trim(strip_tags($this->content_html, '<br>'));
            $this->content = strip_tags($this->content, '<br>');
        }

        if (!$this->content_html) {
            return $this->cms_template->renderJSON([
                'error'   => true,
                'message' => ERR_VALIDATE_REQUIRED,
                'html'    => false
            ]);
        }

        return call_user_func([$this, 'run' . ucfirst($this->request->get('action'))]);
    }

    /**
     * Превью комментария
     * @return json
     */
    private function runPreview() {

        if (!$this->cms_user->is_logged && empty($this->options['is_guests'])) {
            return cmsCore::error404();
        }

        return $this->cms_template->renderJSON([
            'error' => false,
            'html'  => cmsEventsManager::hook('parse_text', $this->content_html)
        ]);
    }

    /**
     * Добавление комментария
     * @return json
     */
    private function runAdd() {

        // Собираем данные комментария
        $comment = [
            'parent_id'         => $this->parent_id,
            'target_controller' => $this->target_controller,
            'target_subject'    => $this->target_subject,
            'target_id'         => $this->target_id,
            'content'           => $this->content,
            'content_html'      => $this->content_html,
            'author_ip'         => function ($db) {
                return '\'' . $db->escape(string_iptobin($this->cms_user->ip)) . '\'';
            }
        ];

        // гость
        if (!$this->cms_user->is_logged) {

            $comment['author_name']  = $this->author_name;
            $comment['author_email'] = $this->author_email;

            if (empty($this->options['is_guests'])) {
                return cmsCore::error404();
            }

            if (!empty($this->options['show_author_email']) && !$this->author_email) {

                return $this->cms_template->renderJSON([
                    'error' => true,
                    'message' => LANG_COMMENT_ERROR_EMAIL,
                    'html' => false
                ]);
            }

            if (!$this->author_name) {

                return $this->cms_template->renderJSON([
                    'error'   => true,
                    'message' => LANG_COMMENT_ERROR_NAME,
                    'html'    => false
                ]);
            }

            // запрещенные ip
            if (!empty($this->options['restricted_ips'])) {
                if (string_in_mask_list($this->cms_user->ip, $this->options['restricted_ips'])) {

                    return $this->cms_template->renderJSON([
                        'error'   => true,
                        'message' => LANG_COMMENT_ERROR_IP,
                        'html'    => false
                    ]);
                }
            }

            // запрещенные email
            if (!empty($this->options['show_author_email']) && !empty($this->options['restricted_emails'])) {
                if (string_in_mask_list($this->author_email, $this->options['restricted_emails'])) {

                    return $this->cms_template->renderJSON([
                        'error'   => true,
                        'message' => LANG_COMMENT_ERROR_EMAIL,
                        'html'    => false
                    ]);
                }
            }

            // запрещенные имена
            if (!empty($this->options['restricted_names'])) {
                if (string_in_mask_list($this->author_name, $this->options['restricted_names'])) {

                    return $this->cms_template->renderJSON([
                        'error'   => true,
                        'message' => ERR_VALIDATE_INVALID,
                        'html'    => false
                    ]);
                }
            }

            // комментарии с одного ip
            if (!empty($this->options['guest_ip_delay'])) {

                $last_comment_time = $this->model->getGuestLastCommentTime($this->cms_user->ip);

                $minutes_passed = (time() - $last_comment_time) / 60;

                if ($minutes_passed < $this->options['guest_ip_delay']) {

                    $spellcount = html_spellcount($this->options['guest_ip_delay'], LANG_MINUTE1, LANG_MINUTE2, LANG_MINUTE10);

                    return $this->cms_template->renderJSON([
                        'error'   => true,
                        'message' => sprintf(LANG_COMMENT_ERROR_TIME, $spellcount),
                        'html'    => false
                    ]);
                }
            }

        // авторизованный юзер
        } else {

            $comment['user_id'] = $this->cms_user->id;

            $is_user_allowed  = cmsUser::isAllowed('comments', 'add');
            $is_karma_allowed = !cmsUser::isPermittedLimitHigher('comments', 'karma', $this->cms_user->karma);

            if (!$is_user_allowed || !$is_karma_allowed) {
                return cmsCore::error404();
            }
        }

        // Получаем модель целевого контроллера
        $target_model = cmsCore::getModel($this->target_controller);

        // Получаем URL и заголовок комментируемой страницы
        $target_info = $target_model->getTargetItemInfo($this->target_subject, $this->target_id);
        if (!$target_info) {
            return $this->cms_template->renderJSON([
                'error'   => true,
                'message' => LANG_COMMENT_ERROR
            ]);
        }

        $comment['target_url']   = $target_info['url'];
        $comment['target_title'] = $target_info['title'];
        $comment['is_private']   = empty($target_info['is_private']) ? false : $target_info['is_private'];

        // проверяем модерацию
        $comment['is_approved'] = $this->isApproved($comment);

        list($comment, $permissions) = cmsEventsManager::hook('comment_add_permissions', [
            $comment,
            ['error' => false, 'message' => '']
        ]);

        if ($permissions['error']) {
            return $this->cms_template->renderJSON($permissions);
        }

        // Сохраняем комментарий
        $comment_id = $this->model->addComment(cmsEventsManager::hook('comment_before_add', $comment, null, $this->request));

        // успешно добавился?
        if (!$comment_id) {
            return $this->cms_template->renderJSON([
                'error'   => true,
                'message' => LANG_COMMENT_ERROR
            ]);
        }

        // Получаем список из одного комментария
        $comments = $this->model->filterEqual('id', $comment_id)->
                disableApprovedFilter()->getComments($this->getCommentActions());

        // Добавленный комментарий
        $comment = reset($comments);

        $comment['content_html'] = cmsEventsManager::hook('parse_text', $comment['content_html']);

        // Уведомление модерации
        if (!$comment['is_approved']) {

            return $this->cms_template->renderJSON([
                'error'       => false,
                'on_moderate' => true,
                'message'     => LANG_COMMENTS_MODERATE_HINT . ' ' . $this->notifyModerators($comment)
            ]);

        } else {

            // Уведомляем модель целевого контента об изменении количества комментариев
            $comments_count = $this->model->
                    filterEqual('target_controller', $this->target_controller)->
                    filterEqual('target_subject', $this->target_subject)->
                    filterEqual('target_id', $this->target_id)->
                    getCommentsCount();

            $this->model->resetFilters();

            $target_model->updateCommentsCount($this->target_subject, $this->target_id, $comments_count);

            $parent_comment = $comment['parent_id'] ? $this->model->getComment($comment['parent_id']) : false;

            // Уведомляем подписчиков
            $this->notifySubscribers($comment, $parent_comment);

            // Уведомляем об ответе на комментарий
            if ($parent_comment) {
                $this->notifyParent($comment, $parent_comment);
            }

            $comment = cmsEventsManager::hook('comment_after_add', $comment, null, $this->request);
        }

        // получаем опции, если есть
        $target_options = [];
        if (method_exists($target_model, 'getCommentsOptions')) {
            $target_options = $target_model->getCommentsOptions($this->target_subject);
        }

        $template_name = !empty($target_options['template']) ? $target_options['template'] : $this->comment_template;

        // Формируем и возвращаем результат
        return $this->cms_template->renderJSON([
            'error'     => false,
            'message'   => LANG_COMMENT_SUCCESS,
            'id'        => $comment_id,
            'parent_id' => isset($comment['parent_id']) ? $comment['parent_id'] : 0,
            'level'     => isset($comment['level']) ? $comment['level'] : 0,
            'html'      => $this->cms_template->render($template_name, [
                'comments'       => [$comment],
                'target_user_id' => $this->target_user_id,
                'user'           => $this->cms_user,
                'is_levels'      => true,
                'is_controls'    => true,
                'is_show_target' => false
            ], new cmsRequest(array(), cmsRequest::CTX_INTERNAL))
        ]);
    }

    /**
     * Редактирование комментария
     * @return json
     */
    private function runUpdate() {

        if (!$this->cms_user->is_logged || !cmsUser::isAllowed('comments', 'edit')) {
            return cmsCore::error404();
        }

        $comment = $this->model->getComment($this->comment_id);
        if (!$comment) {
            return $this->cms_template->renderJSON([
                'error'   => true,
                'message' => '404'
            ]);
        }

        if (!cmsUser::isAllowed('comments', 'edit', 'all')) {
            if (cmsUser::isAllowed('comments', 'edit', 'own') && $comment['user']['id'] != $this->cms_user->id) {
                return $this->cms_template->renderJSON([
                    'error'   => true,
                    'message' => LANG_COMMENT_ERROR
                ]);
            }
        }

        if (cmsUser::isPermittedLimitReached('comments', 'times', ((time() - strtotime($comment['date_pub'])) / 60))) {
            return $this->cms_template->renderJSON([
                'error'   => true,
                'message' => 'Time is over'
            ]);
        }

        list($this->comment_id, $this->content, $this->content_html, $data) = cmsEventsManager::hook('comment_before_update', [
            $this->comment_id,
            $this->content,
            $this->content_html,
            []
       ], null, $this->request);

        $this->model->updateCommentContent($this->comment_id, $this->content, $this->content_html, $data);

        $comment = $this->model->getComment($this->comment_id);

        $comment = cmsEventsManager::hook('comment_after_update', $comment, null, $this->request);

        $comment['content_html'] = cmsEventsManager::hook('parse_text', $comment['content_html']);

        return $this->cms_template->renderJSON([
            'error'     => false,
            'message'   => LANG_SUCCESS_MSG,
            'id'        => $this->comment_id,
            'parent_id' => isset($comment['parent_id']) ? $comment['parent_id'] : 0,
            'level'     => isset($comment['level']) ? $comment['level'] : 0,
            'html'      => $comment['content_html']
        ]);
    }

}
