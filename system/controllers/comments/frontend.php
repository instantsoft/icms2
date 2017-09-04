<?php

class comments extends cmsFrontend {

    public $target_controller;
    public $target_subject;

	protected $useOptions = true;
    public $useSeoOptions = true;

	public function __construct($request){

        parent::__construct($request);

        $this->target_controller = $this->request->get('target_controller', '');
        $this->target_subject    = $this->request->get('target_subject', '');
        $this->target_id         = $this->request->get('target_id', 0);
        $this->target_user_id    = $this->request->get('target_user_id', 0);

    }

    public function getNativeComments() {

        if(cmsUser::isAllowed('comments', 'is_moderator')){
            $this->model->disableApprovedFilter();
        }

        cmsEventsManager::hook('comments_list_filter', $this->model);

        $comments = $this->model->filterCommentTarget(
                $this->target_controller,
                $this->target_subject,
                $this->target_id
            )->getComments();

        $comments = cmsEventsManager::hook('comments_before_list', $comments);

        $is_tracking = $this->model->filterCommentTarget(
                $this->target_controller,
                $this->target_subject,
                $this->target_id
            )->getTracking($this->cms_user->id);

        $is_highlight_new = $this->request->hasInQuery('new_comments');

        if ($is_highlight_new && !$this->cms_user->is_logged) { $is_highlight_new = false; }

        $csrf_token_seed = implode('/', array($this->target_controller, $this->target_subject, $this->target_id));

        $rss_link = '';
        if ($this->isControllerEnabled('rss') && $this->model->isRssFeedEnable()){
            $rss_link = href_to('rss', 'feed', 'comments').'?'.http_build_query(array(
                'tc' => $this->target_controller,
                'ts' => $this->target_subject,
                'ti' => $this->target_id
            ));
        }

        return array(
            'name'  => 'icms',
            'title' => ($comments ? html_spellcount(sizeof($comments), LANG_COMMENT1, LANG_COMMENT2, LANG_COMMENT10) : LANG_COMMENTS),
            'html'  => $this->cms_template->renderInternal($this, 'list', array(
                'user'              => $this->cms_user,
                'target_controller' => $this->target_controller,
                'target_subject'    => $this->target_subject,
                'target_id'         => $this->target_id,
                'target_user_id'    => $this->target_user_id,
                'is_tracking'       => $is_tracking,
                'is_highlight_new'  => $is_highlight_new,
                'comments'          => $comments,
                'csrf_token_seed'   => $csrf_token_seed,
                'rss_link'          => $rss_link,
                'is_can_rate'       => cmsUser::isAllowed('comments', 'rate')
            ))
        );

    }

    public function getWidget(){

        $comment_systems = cmsEventsManager::hookAll('comment_systems', $this, array());

        if(empty($this->options['disable_icms_comments']) || !$comment_systems){
            array_unshift($comment_systems, $this->getNativeComments());
        }

        return $this->cms_template->renderInternal($this, 'tab_list', array(
            'comment_systems' => $comment_systems
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

        $messenger = cmsCore::getController('messages');

        $messenger->addRecipients($subscribers);

        $messenger->sendNoticeEmail('comments_new', array(
            'page_url'        => href_to_abs($comment['target_url']) . "#comment_{$comment['id']}",
            'page_title'      => $comment['target_title'],
            'author_url'      => href_to_abs('users', $comment['user_id']),
            'author_nickname' => $comment['user_nickname'],
            'comment'         => $comment['content']
        ));

    }

    public function notifyParent($comment, $parent_comment){

        if ($comment['user_id'] && ($comment['user_id'] == $parent_comment['user_id'])) { return; }

        $messenger = cmsCore::getController('messages');

		$is_guest_parent = !$parent_comment['user_id'] && $parent_comment['author_email'];
		$is_guest_comment = !$comment['user_id'];

		$page_url = href_to_abs($comment['target_url']) . "#comment_{$comment['id']}";

		$letter_data = array(
            'page_url' => $page_url,
            'page_title' => $comment['target_title'],
            'author_url' => $is_guest_comment ? $page_url : href_to_abs('users', $comment['user_id']),
            'author_nickname' => $is_guest_comment ? $comment['author_name'] : $comment['user_nickname'],
            'comment' => $comment['content'],
            'original' => $parent_comment['content'],
        );

		if (!$is_guest_parent){
			$messenger->addRecipient($parent_comment['user_id']);
			$messenger->sendNoticeEmail('comments_reply', $letter_data);
		}

		if ($is_guest_parent){
			$letter_data['nickname'] = $parent_comment['author_name'];
			$to = array('name' => $parent_comment['author_name'], 'email' => $parent_comment['author_email']);
			$letter = array('name' => 'comments_reply');
			$messenger->sendEmail($to, $letter, $letter_data);
		}

    }

//============================================================================//
//============================================================================//

    public function renderCommentsList($page_url, $dataset_name=false){

        $page = $this->request->get('page', 1);
        $perpage = (empty($this->options['limit']) ? 15 : $this->options['limit']);

        // Фильтр приватности
        if ((!$dataset_name || $dataset_name == 'all') && !cmsUser::isAllowed('comments', 'view_all')){
            $this->model->filterPrivacy();
        }

        if(!$this->model->order_by){
            $this->model->orderBy('date_pub', 'desc');
        }

        // Постраничный вывод
        $this->model->limitPage($page, $perpage);

        // Скрываем удаленные
        $this->model->filterIsNull('is_deleted');

        cmsEventsManager::hook('comments_list_filter', $this->model);

        // Получаем количество и список записей
        $total = !empty($this->count) ? $this->count : $this->model->getCommentsCount();
        $items = $this->model->getComments();

        // если запрос через URL
        if($this->request->isStandard()){
            if(!$items && $page > 1){ cmsCore::error404(); }
        }

        $items = cmsEventsManager::hook('comments_before_list', $items);

        return $this->cms_template->renderInternal($this, 'list_index', array(
            'filters'        => array(),
            'dataset_name'   => $dataset_name,
            'page_url'       => $page_url,
            'page'           => $page,
            'perpage'        => $perpage,
            'total'          => $total,
            'items'          => $items,
            'user'           => $this->cms_user,
            'target_user_id' => $this->target_user_id,
        ));

    }

    public function getDatasets(){

        $datasets = array();

        // Все (новые)
        $datasets['all'] = array(
            'name' => 'all',
            'title' => LANG_COMMENTS_DS_ALL,
        );

        // Мои друзья
        if ($this->cms_user->is_logged){
            $datasets['friends'] = array(
                'name' => 'friends',
                'title' => LANG_COMMENTS_DS_FRIENDS,
                'filter' => function($model){
                    return $model->filterFriends(cmsUser::getInstance()->id);
                }
            );
        }

        // Только мои
        if ($this->cms_user->is_logged){
            $datasets['my'] = array(
                'name' => 'my',
                'title' => LANG_COMMENTS_DS_MY,
                'filter' => function($model){
                    $model->filterEqual('user_id', cmsUser::getInstance()->id);
                    return $model->disableApprovedFilter();
                }
            );
        }

        // модерация
        if(cmsUser::isAllowed('comments', 'is_moderator')){
            $datasets['moderation'] = array(
                'name'  => 'moderation',
                'title' => LANG_MODERATION,
                'filter' => function($model){
                    $model->disableApprovedFilter();
                    return $model->filterNotEqual('is_approved', 1);
                }
            );
        }

        return cmsEventsManager::hook('comments_datasets', $datasets);

    }

    public function isApproved($comment) {

        // модерация для гостей
        if (!empty($comment['author_name'])){
            return empty($this->options['is_guests_moderate']);
        }

        $is_approved = cmsUser::isAllowed('comments', 'add_approved');

        $is_approved_by_hook = cmsEventsManager::hook('comments_is_approved', array(
            'is_approved' => $is_approved,
            'comment'     => $comment
        ));

        $is_approved_by_hook['is_approved'] = ($is_approved_by_hook['is_approved'] ?
            $is_approved_by_hook['is_approved'] :
            cmsUser::isAllowed('comments', 'is_moderator'));

        return $is_approved_by_hook['is_approved'];

    }

    public function notifyModerators($comment) {

        // проверяем, нет ли уже комментариев на модерации
        $this->model->disableApprovedFilter()->filterNotEqual('is_approved', 1);
            $count = $this->model->getCommentsCount();
        $this->model->resetFilters();

        // если больше одного, значит уже уведомления рассылали
        if($count > 1){ return false; }

        $messenger = cmsCore::getController('messages');

        // рассылаем модераторам уведомления
        $moderators = $this->model->getCommentsModerators();

        foreach ($moderators as $moderator) {

            $messenger->clearRecipients()->addRecipient($moderator['id']);

            $messenger->sendNoticePM(array(
                'content' => LANG_COMMENTS_MODERATE_NOTIFY,
                'actions' => array(
                    'view' => array(
                        'title' => LANG_SHOW,
                        'href'  => href_to('comments', 'index', 'moderation')
                    )
                )
            ));

            if(!$moderator['is_online']){

                $page_url = href_to_abs($comment['target_url']) . "#comment_{$comment['id']}";

                $messenger->sendEmail(
                    array(
                        'email' => $moderator['email'],
                        'name'  => $moderator['nickname']
                    ),
                    'comments_moderate',
                    array(
                        'author_nickname' => !$comment['user_id'] ? $comment['author_name'] : $comment['user_nickname'],
                        'author_url' => !$comment['user_id'] ? $page_url : href_to_abs('users', $comment['user_id']),
                        'page_url'   => $page_url,
                        'comment'    => strip_tags($comment['content_html']),
                        'nickname'   => $moderator['nickname'],
                        'list_url'   => href_to_abs('comments', 'index', 'moderation')
                    )
                );

            }

        }

    }

}
