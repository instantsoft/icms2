<?php

class actionCommentsSubmit extends cmsAction {

    public function run(){

        if (!$this->request->isAjax()){ cmsCore::error404(); }

        $action = $this->request->get('action', '');

		$is_guests_allowed = !empty($this->options['is_guests']);
        $is_guest          = $is_guests_allowed && !$this->cms_user->is_logged;
        $is_user_allowed   = ($this->cms_user->is_logged && cmsUser::isAllowed('comments', 'add')) || $is_guests_allowed;
        $is_karma_allowed  = ($this->cms_user->is_logged && !cmsUser::isPermittedLimitHigher('comments', 'karma', $this->cms_user->karma)) || $is_guests_allowed;
        $is_add_allowed    = $is_user_allowed && $is_karma_allowed;

        if ($action=='add' && !$is_add_allowed){ cmsCore::error404(); }
        if ($action=='update' && !cmsUser::isAllowed('comments', 'edit')){ cmsCore::error404(); }

        $csrf_token        = $this->request->get('csrf_token', '');
        $target_controller = $this->request->get('tc', '');
        $target_subject    = $this->request->get('ts', '');
        $target_id         = $this->request->get('ti', '');
        $target_user_id    = $this->request->get('tud', '');
        $parent_id         = $this->request->get('parent_id', 0);
        $comment_id        = $this->request->get('id', 0);
        $content           = $this->request->get('content', '');

        if ($is_guest){

			$author_name  = $this->request->get('author_name', '');
            $author_email = $this->request->get('author_email', '');

            if (!$author_name){
				return $this->cms_template->renderJSON(array('error' => true, 'message' => LANG_COMMENT_ERROR_NAME, 'html' => false));
			}
			if ($author_email && !preg_match("/^([a-zA-Z0-9\._-]+)@([a-zA-Z0-9\._-]+)\.([a-zA-Z]{2,4})$/i", $author_email)){
				return $this->cms_template->renderJSON(array('error' => true, 'message' => LANG_COMMENT_ERROR_EMAIL, 'html' => false));
			}

            if (!empty($this->options['restricted_ips'])){
                if (string_in_mask_list($this->cms_user->ip, $this->options['restricted_ips'])){
                    return $this->cms_template->renderJSON(array('error' => true, 'message' => LANG_COMMENT_ERROR_IP, 'html' => false));
                }
            }

            if (!empty($this->options['guest_ip_delay'])){
                $last_comment_time = $this->model->getGuestLastCommentTime($this->cms_user->ip);
                $now_time = time();
                $minutes_passed = ($now_time - $last_comment_time) / 60;
                if ($minutes_passed < $this->options['guest_ip_delay']){
                    $spellcount = html_spellcount($this->options['guest_ip_delay'], LANG_MINUTE1, LANG_MINUTE2, LANG_MINUTE10);
                    return $this->cms_template->renderJSON(array('error' => true, 'message' => sprintf(LANG_COMMENT_ERROR_TIME, $spellcount), 'html' => false));
                }
            }

		}

        // Проверяем валидность
        $is_valid = $target_controller && $target_subject && $target_id &&
                    ($this->validate_sysname($target_controller)===true) &&
                    ($this->validate_sysname($target_subject)===true) &&
                    is_numeric($target_id) &&
                    is_numeric($parent_id) &&
                    cmsCore::isControllerExists($target_controller) &&
                    cmsCore::isModelExists($target_controller) &&
                    (!$comment_id || is_numeric($comment_id)) &&
                    cmsForm::validateCSRFToken($csrf_token, false) &&
                    in_array($action, array('add', 'preview', 'update'), true);

        if (!$is_valid){
            return $this->cms_template->renderJSON(array('error' => true, 'message' => LANG_COMMENT_ERROR));
        }

        // Типографируем текст
        $content_html = cmsEventsManager::hook('html_filter', $content);

		if (!$content_html){
			return $this->cms_template->renderJSON(array(
				'error'   => true,
                'message' => ERR_VALIDATE_REQUIRED,
                'html'    => false
            ));
		}

        //
        // Превью комментария
        //
        if ($action=='preview'){
            return $this->cms_template->renderJSON(array(
                'error' => false,
                'html' => cmsEventsManager::hook('parse_text', $content_html)
            ));
        }

        //
        // Редактирование комментария
        //
        if ($action=='update'){

            $comment = $this->model->getComment($comment_id);

            if (!cmsUser::isAllowed('comments', 'edit', 'all')) {
                if (cmsUser::isAllowed('comments', 'edit', 'own') && $comment['user']['id'] != $this->cms_user->id) {
                    return $this->cms_template->renderJSON(array('error' => true, 'message' => LANG_COMMENT_ERROR));
                }
            }

            list($comment_id, $content, $content_html) = cmsEventsManager::hook('comment_before_update', array($comment_id, $content, $content_html));

            $this->model->updateCommentContent($comment_id, $content, $content_html);

            $comment_html = cmsEventsManager::hook('parse_text', $content_html);

        }

        //
        // Добавление комментария
        //
        if ($action=='add'){

            // Собираем данные комментария
            $comment = array(
                'user_id'           => $this->cms_user->id,
                'parent_id'         => $parent_id,
                'target_controller' => $target_controller,
                'target_subject'    => $target_subject,
                'target_id'         => $target_id,
                'content'           => $content,
                'content_html'      => $content_html,
                'author_url'        => $this->cms_user->ip
            );

			if ($is_guest){
				$comment['author_name']  = $author_name;
                $comment['author_email'] = $author_email;
            }

            // Получаем модель целевого контроллера
            $target_model = cmsCore::getModel( $target_controller );

            // Получаем URL и заголовок комментируемой страницы
            $target_info = $target_model->getTargetItemInfo($target_subject, $target_id);

            if ($target_info){

                $comment['target_url']   = $target_info['url'];
                $comment['target_title'] = $target_info['title'];
                $comment['is_private']   = empty($target_info['is_private']) ? false : $target_info['is_private'];

                // проверяем модерацию
                $comment['is_approved'] = $this->isApproved($comment);

                list($comment, $permissions) = cmsEventsManager::hook('comment_add_permissions', array(
                    $comment,
                    array('error'=>false, 'message'=>'')
                ));

                if($permissions['error']){
                    return $this->cms_template->renderJSON($permissions);
                }

                // Сохраняем комментарий
                $comment_id = $this->model->addComment(cmsEventsManager::hook('comment_before_add', $comment));

            }

            if ($comment_id){

                // Получаем и рендерим добавленный комментарий
                $comment = $this->model->getComment($comment_id);
                $comment['content_html'] = cmsEventsManager::hook('parse_text', $comment['content_html']);
                $comment_html = $this->cms_template->render('comment', array(
                    'comments'       => array($comment),
                    'target_user_id' => $target_user_id,
                    'user'           => $this->cms_user
                ), new cmsRequest(array(), cmsRequest::CTX_INTERNAL));

                // Уведомление модерации
                if(!$comment['is_approved']){

                    $this->notifyModerators($comment);

                    return $this->cms_template->renderJSON(array(
                        'error'       => false,
                        'on_moderate' => true,
                        'message'     => LANG_COMMENTS_MODERATE_HINT
                    ));

                } else {

                    // Уведомляем модель целевого контента об изменении количества комментариев
                    $comments_count = $this->model->
                                                filterEqual('target_controller', $target_controller)->
                                                filterEqual('target_subject', $target_subject)->
                                                filterEqual('target_id', $target_id)->
                                                getCommentsCount();

                    $this->model->resetFilters();

                    $target_model->updateCommentsCount($target_subject, $target_id, $comments_count);

                    $parent_comment = $parent_id ? $this->model->getComment($parent_id) : false;

                    // Уведомляем подписчиков
                    $this->notifySubscribers($comment, $parent_comment);

                    // Уведомляем об ответе на комментарий
                    if ($parent_comment){ $this->notifyParent($comment, $parent_comment); }

                    $comment = cmsEventsManager::hook('comment_after_add', $comment);

                }

            }

        }

        // Формируем и возвращаем результат
        $result = array(
            'error'     => $comment_id ? false : true,
            'message'   => $comment_id ? LANG_COMMENT_SUCCESS : LANG_COMMENT_ERROR,
            'id'        => $comment_id,
            'parent_id' => isset($comment['parent_id']) ? $comment['parent_id'] : 0,
            'level'     => isset($comment['level']) ? $comment['level'] : 0,
            'html'      => isset($comment_html) ? $comment_html : false
        );

        return $this->cms_template->renderJSON($result);

    }

}
