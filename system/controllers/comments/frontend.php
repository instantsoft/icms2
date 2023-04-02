<?php
/**
 * @property \modelComments $model
 */
class comments extends cmsFrontend {

    public $target_controller;
    public $target_subject;
    public $target_id;
    public $target_user_id;
    public $labels;
    public $comments_title;
    public $comment_template = 'comment';

	protected $useOptions = true;
    public $useSeoOptions = true;

    protected $unknown_action_as_index_param = true;

	public function __construct($request){

        parent::__construct($request);

        $this->target_controller = $this->request->get('target_controller', '');
        $this->target_subject    = $this->request->get('target_subject', '');
        $this->target_id         = $this->request->get('target_id', 0);
        $this->target_user_id    = $this->request->get('target_user_id', 0);

        $this->setLabels($this->request->get('labels', []));

    }

    public function setLabels($labels) {

        $this->labels = (object) array_merge([
            'comments'   => LANG_COMMENTS,
            'spellcount' => LANG_COMMENT1 . '|' . LANG_COMMENT2 . '|' . LANG_COMMENT10,
            'add'        => LANG_COMMENT_ADD,
            'none'       => LANG_COMMENTS_NONE,
            'low_karma'  => LANG_COMMENTS_LOW_KARMA,
            'login'      => LANG_COMMENTS_LOGIN,
            'track'      => LANG_COMMENTS_TRACK,
            'refresh'    => LANG_COMMENTS_REFRESH,
            'commenting' => LANG_RULE_CONTENT_COMMENT
        ], array_filter($labels));

        return $this;

    }

    public function getNativeComments() {

        $is_moderator = $this->controller_moderation->userIsContentModerator($this->name, $this->cms_user->id);

        if($is_moderator){
            $this->model->disableApprovedFilter();
        }

        cmsEventsManager::hook('comments_list_filter', $this->model);

        $comments_count = $this->model->filterCommentTarget(
                $this->target_controller,
                $this->target_subject,
                $this->target_id
            )->getCommentsCount();
        $comments = $this->model->joinCommentsRating($this->cms_user->id)->
                getComments($this->getCommentActions());

        $comments = cmsEventsManager::hook('comments_before_list', $comments);
        list($comments, $comments_count) = cmsEventsManager::hook('comments_before_list_this', [$comments, $comments_count, $this]);

        $is_tracking = $this->model->filterCommentTarget(
                $this->target_controller,
                $this->target_subject,
                $this->target_id
            )->getTracking($this->cms_user->id);

        $is_highlight_new = $this->request->hasInQuery('new_comments');

        if ($is_highlight_new && !$this->cms_user->is_logged) { $is_highlight_new = false; }

        $csrf_token_seed = implode('/', array($this->target_controller, $this->target_subject, $this->target_id));

        $rss_link = '';
        if ($comments && $this->isControllerEnabled('rss') && $this->model->isRssFeedEnable()){
            $rss_link = href_to('rss', 'feed', 'comments').'?'.http_build_query([
                'tc' => $this->target_controller,
                'ts' => $this->target_subject,
                'ti' => $this->target_id
            ]);
        }

        if(!$this->comments_title){
            $this->comments_title = ($comments_count ? html_spellcount($comments_count, $this->labels->spellcount) : $this->labels->comments);
        }

        $editor_params = cmsCore::getController('wysiwygs')->getEditorParams([
            'editor'  => $this->options['editor'],
            'presets' => $this->options['editor_presets']
        ]);

        $editor_params['options']['id'] = 'content';

        // Контекст использования
        $editor_params['options']['upload_params'] = [
            'target_controller' => 'comments',
            'target_subject' => $this->target_subject
        ];

        return [
            'name'  => 'icms',
            'icon'  => $rss_link ? ['href' => $rss_link, 'icon' => 'rss', 'title' => 'RSS', 'class' => 'inline_rss_icon'] : [],
            'title' => $this->comments_title,
            'html'  => $this->cms_template->renderInternal($this, 'list', [
                'user'              => $this->cms_user,
                'editor_params'     => $editor_params,
                'target_controller' => $this->target_controller,
                'target_subject'    => $this->target_subject,
                'target_id'         => $this->target_id,
                'target_user_id'    => $this->target_user_id,
                'is_karma_allowed'  => $this->cms_user->is_logged && !cmsUser::isPermittedLimitHigher('comments', 'karma', $this->cms_user->karma),
                'is_guests_allowed' => !empty($this->options['is_guests']),
                'can_add'           => cmsUser::isAllowed('comments', 'add') || (!$this->cms_user->is_logged && !empty($this->options['is_guests'])),
                'is_tracking'       => $is_tracking,
                'is_highlight_new'  => $is_highlight_new,
                'comments'          => $comments,
                'csrf_token_seed'   => $csrf_token_seed,
                'rss_link'          => $rss_link,
                'guest_name'        => cmsUser::getCookie('comments_guest_name', 'string', function ($cookie){ return trim(strip_tags($cookie)); }),
                'guest_email'       => cmsUser::getCookie('comments_guest_email', 'string', function ($cookie){ return trim(strip_tags($cookie)); }),
                'is_can_rate'       => cmsUser::isAllowed('comments', 'rate')
            ])
        ];

    }

    public function getWidget(){

        $comment_systems = cmsEventsManager::hookAll('comment_systems', $this, array());

        if(empty($this->options['disable_icms_comments']) || !$comment_systems){
            array_unshift($comment_systems, $this->getNativeComments());
        }

        return $this->cms_template->renderInternal($this, 'tab_list', array(
            'comment_systems' => $comment_systems,
            'target_controller' => $this->target_controller,
            'target_subject'    => $this->target_subject,
            'target_id'         => $this->target_id,
            'target_user_id'    => $this->target_user_id
        ));

    }

//============================================================================//
//============================================================================//

    public function notifySubscribers($comment, $parent_comment=false){

        $subscribers = $this->model->filterCommentTarget(
                $comment['target_controller'],
                $comment['target_subject'],
                $comment['target_id']
            )->getTrackingUsers();

        if (!$subscribers) { return; }

        // удаляем автора комментария из списка подписчиков
        $user_key = array_search($comment['user_id'], $subscribers);
        if ($user_key!==false) { unset($subscribers[$user_key]); }

        // удаляем автора родительского комментария из списка подписчиков,
        // поскольку он получит отдельное уведомление об ответе на комментарий
        if ($parent_comment){
            $parent_user_key = array_search($parent_comment['user_id'], $subscribers);
            if ($parent_user_key!==false) { unset($subscribers[$parent_user_key]); }
        }

        // проверяем что кто-либо остался в списке
        if (!$subscribers) { return; }

        $notice_data = [
            'page_url'        => href_to_abs($comment['target_url']) . "#comment_{$comment['id']}",
            'page_title'      => $comment['target_title'],
            'author_url'      => href_to_profile($comment['user'], false, true),
            'author_nickname' => $comment['user_nickname'],
            'comment'         => $comment['content']
        ];

        $messenger = cmsCore::getController('messages');

        $messenger->addRecipients($subscribers);

        $messenger->sendNoticePM(array(
            'content' => sprintf(LANG_COMMENTS_NEW_NOTIFY, $notice_data['author_url'], $notice_data['author_nickname'], $notice_data['page_title']),
            'actions' => array(
                'view' => array(
                    'title' => LANG_COMMENTS_VIEW,
                    'href'  => $notice_data['page_url']
                )
            )
        ), 'comments_new');

        $messenger->sendNoticeEmail('comments_new', $notice_data);

    }

    public function notifyParent($comment, $parent_comment){

        $success = false;

        if ($comment['user_id'] && ($comment['user_id'] == $parent_comment['user_id'])) { return $success; }

        $messenger = cmsCore::getController('messages');

		$is_guest_parent  = !$parent_comment['user_id'];
		$is_guest_comment = !$comment['user_id'];

		$page_url = href_to_abs($comment['target_url']) . "#comment_{$comment['id']}";

		$letter_data = array(
            'page_url'        => $page_url,
            'page_title'      => $comment['target_title'],
            'author_url'      => $is_guest_comment ? $page_url : href_to_profile($comment['user'], false, true),
            'author_nickname' => $is_guest_comment ? $comment['author_name'] : $comment['user_nickname'],
            'comment'         => $comment['content'],
            'original'        => $parent_comment['content']
        );

		if (!$is_guest_parent){

			$success = $messenger->addRecipient($parent_comment['user_id'])->
                    sendNoticeEmail('comments_reply', $letter_data);

		}

		if ($is_guest_parent && $parent_comment['author_email']){

			$letter_data['nickname'] = $parent_comment['author_name'];
			$to = array('name' => $parent_comment['author_name'], 'email' => $parent_comment['author_email']);
			$letter = array('name' => 'comments_reply');

			$success = $messenger->sendEmail($to, $letter, $letter_data);

		}

        return $success;

    }

//============================================================================//
//============================================================================//

    public function renderCommentsList($page_url, $dataset_name = false) {

        $page    = $this->request->get('page', 1);
        $perpage = (empty($this->options['limit']) ? 15 : $this->options['limit']);

        // Фильтр приватности
        if ((!$dataset_name || $dataset_name == 'all') && !cmsUser::isAllowed('comments', 'view_all')) {
            $this->model->filterPrivacy();
        }

        if (!$this->model->order_by) {
            $this->model->orderBy('date_pub', 'desc');
        }

        // Постраничный вывод
        $this->model->limitPage($page, $perpage);

        // Скрываем удаленные
        $this->model->filterIsNull('is_deleted');

        cmsEventsManager::hook('comments_list_filter', $this->model);

        // Получаем количество и список записей
        $total = !empty($this->count) ? $this->count : $this->model->getCommentsCount();

        cmsEventsManager::hook('comments_list_filter_after_count', $this->model);

        $items = $this->model->getComments();

        // если запрос через URL
        if ($this->request->isStandard()) {
            if (!$items && $page > 1) {
                cmsCore::error404();
            }
        }

        $items = cmsEventsManager::hook('comments_before_list', $items);

        return $this->cms_template->renderInternal($this, 'list_index', [
            'filters'        => [],
            'dataset_name'   => $dataset_name,
            'page_url'       => $page_url,
            'page'           => $page,
            'perpage'        => $perpage,
            'total'          => $total,
            'items'          => $items,
            'user'           => $this->cms_user,
            'target_user_id' => $this->target_user_id,
        ]);
    }

    public function getDatasets() {

        $datasets = [];

        // Все (новые)
        $datasets['all'] = [
            'name'  => 'all',
            'title' => LANG_COMMENTS_DS_ALL,
        ];

        // Мои друзья
        if ($this->cms_user->is_logged) {
            $datasets['friends'] = [
                'name'   => 'friends',
                'title'  => LANG_COMMENTS_DS_FRIENDS,
                'filter' => function ($model) {
                    return $model->filterFriends(cmsUser::getInstance()->id);
                }
            ];
        }

        // Только мои
        if ($this->cms_user->is_logged) {
            $datasets['my'] = [
                'name'   => 'my',
                'title'  => LANG_COMMENTS_DS_MY,
                'filter' => function ($model) {
                    $model->filterEqual('user_id', cmsUser::getInstance()->id);
                    return $model->disableApprovedFilter();
                }
            ];
        }

        return cmsEventsManager::hook('comments_datasets', $datasets);
    }

    public function isApproved($comment) {

        // модерация для гостей
        if (!empty($comment['author_name'])) {
            return empty($this->options['is_guests_moderate']);
        }

        $is_approved = cmsUser::isAllowed('comments', 'add_approved');

        if (!$is_approved && $this->controller_moderation->userIsContentModerator($this->name, $this->cms_user->id, $comment)) {
            $is_approved = true;
        }

        $is_approved_by_hook = cmsEventsManager::hook('comments_is_approved', [
            'is_approved' => $is_approved,
            'comment'     => $comment
        ]);

        return $is_approved_by_hook['is_approved'];
    }

    public function notifyModerators($comment) {

        $comment['page_url'] = href_to_abs($comment['target_url']) . '#comment_' . $comment['id'];
        $comment['url']      = $comment['target_url'] . '#comment_' . $comment['id'];
        $comment['title']    = $comment['target_title'];

        return $this->controller_moderation->requestModeration($this->name, $comment, true, sprintf(LANG_COMMENTS_MODERATE_NOTIFY, $comment['content_html']));
    }

    public function getCommentActions($params = []) {

        $is_can_add = ($this->cms_user->is_logged && cmsUser::isAllowed('comments', 'add')) ||
                (!$this->cms_user->is_logged && !empty($this->options['is_guests']));

        $perms = [
            'actions_times' => cmsUser::getPermissionValue('comments', 'times'),
            'is_edit_all'   => cmsUser::isAllowed('comments', 'edit', 'all'),
            'is_edit_own'   => cmsUser::isAllowed('comments', 'edit', 'own'),
            'is_delete_all' => cmsUser::isAllowed('comments', 'delete', 'all'),
            'is_delete_own' => cmsUser::isAllowed('comments', 'delete', 'own')
        ];

        $actions = [
            [
                'title'   => LANG_COMMENTS_APPROVE,
                'icon'    => 'check',
                'href'    => '#approve',
                'class'   => 'btn-outline-success mr-1 approve hide_approved',
                'onclick' => 'return icms.comments.approve({id})',
                'handler' => function($comment){
                    return !$comment['is_approved'];
                }
            ],
            [
                'title'   => LANG_REPLY,
                'icon'    => 'reply',
                'href'    => '#reply',
                'onclick' => 'return icms.comments.add({id})',
                'handler' => function($comment) use($is_can_add, $params) {
                    return $is_can_add && empty($params['is_moderator']);
                },
                'handler_class' => function($comment){
                    $class = 'btn-link reply mr-1';
                    if(!$comment['is_approved']){
                        $class .= ' no_approved';
                    }
                    return $class;
                }
            ],
            [
                'hint'    => LANG_EDIT,
                'icon'    => 'edit',
                'href'    => '#edit',
                'class'   => 'btn-outline-secondary edit',
                'onclick' => 'return icms.comments.edit({id})',
                'handler' => function($comment) use($perms, $params) {

                    if ($perms['actions_times'] && ((time() - strtotime($comment['date_pub']))/60 >= $perms['actions_times'])){
                        $perms['is_edit_own'] = false;
                    }

                    $is_can_edit = $perms['is_edit_all'] || ($perms['is_edit_own'] && $comment['user']['id'] == $this->cms_user->id);

                    return $is_can_edit && empty($params['is_moderator']);
                }
            ],
            [
                'hint'    => LANG_DELETE,
                'icon'    => 'trash',
                'href'    => '#delete',
                'class'   => 'btn-outline-danger',
                'onclick' => 'return icms.comments.remove({id}, false)',
                'handler' => function($comment) use($perms) {

                    if ($perms['actions_times'] && ((time() - strtotime($comment['date_pub']))/60 >= $perms['actions_times'])){
                        $perms['is_delete_own'] = false;
                    }

                    $is_can_delete = $perms['is_delete_all'] || ($perms['is_delete_own'] && $comment['user']['id'] == $this->cms_user->id);

                    return $is_can_delete && $comment['is_approved'];
                }
            ],
            [
                'hint'    => LANG_DECLINE,
                'icon'    => 'trash',
                'href'    => '#delete',
                'class'   => 'btn-outline-danger',
                'onclick' => 'return icms.comments.remove({id}, true)',
                'handler' => function($comment) use($perms) {

                    if ($perms['actions_times'] && ((time() - strtotime($comment['date_pub']))/60 >= $perms['actions_times'])){
                        $perms['is_delete_own'] = false;
                    }

                    $is_can_delete = $perms['is_delete_all'] || ($perms['is_delete_own'] && $comment['user']['id'] == $this->cms_user->id);

                    return $is_can_delete && !$comment['is_approved'];
                }
            ]
        ];

        list($params, $actions) = cmsEventsManager::hook('comments_item_actions', [$params, $actions]);

        return $actions;
    }

}
