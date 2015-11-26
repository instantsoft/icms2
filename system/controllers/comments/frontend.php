<?php

class comments extends cmsFrontend {

    protected $target_controller;
    protected $target_subject;

	protected $useOptions = true;

	public function __construct($request){

        parent::__construct($request);

        $this->target_controller = $this->request->get('target_controller');
        $this->target_subject    = $this->request->get('target_subject');
        $this->target_id         = $this->request->get('target_id');
        $this->target_user_id    = $this->request->get('target_user_id');

    }

//============================================================================//
//============================================================================//

    public function getWidget(){

        $user = cmsUser::getInstance();

        $comments = $this->model->
                            lockFilters()->
                            filterEqual('target_controller', $this->target_controller)->
                            filterEqual('target_subject', $this->target_subject)->
                            filterEqual('target_id', $this->target_id)->
                            getComments();

        $comments = cmsEventsManager::hook('comments_before_list', $comments);

        $is_tracking = $this->model->getTracking($user->id);

        $is_highlight_new = $this->request->hasInQuery('new_comments');

        if ($is_highlight_new && !$user->is_logged) { cmsCore::error404(); }

        $csrf_token_seed = implode('/', array($this->target_controller, $this->target_subject, $this->target_id));

        return cmsTemplate::getInstance()->renderInternal($this, 'list', array(
            'user'              => $user,
            'target_controller' => $this->target_controller,
            'target_subject'    => $this->target_subject,
            'target_id'         => $this->target_id,
            'target_user_id'    => $this->target_user_id,
            'is_tracking'       => $is_tracking,
            'is_highlight_new'  => $is_highlight_new,
            'comments'          => $comments,
            'csrf_token_seed'   => $csrf_token_seed,
            'is_can_rate'       => cmsUser::isAllowed('comments', 'rate')
        ));

    }

//============================================================================//
//============================================================================//

    public function notifySubscribers($comment, $parent_comment=false){

        $subscribers = $this->model->
                                filterEqual('target_controller', $comment['target_controller'])->
                                filterEqual('target_subject', $comment['target_subject'])->
                                filterEqual('target_id', $comment['target_id'])->
                                getTrackingUsers();

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
            'page_url' => href_to_abs($comment['target_url']) . "#comment_{$comment['id']}",
            'page_title' => $comment['target_title'],
            'author_url' => href_to_abs('users', $comment['user_id']),
            'author_nickname' => $comment['user_nickname'],
            'comment' => $comment['content']
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

        $user = cmsUser::getInstance();

        $page = $this->request->get('page', 1);
        $perpage = 15;

        // Фильтр приватности
        if (!$dataset_name || $dataset_name == 'all'){
            $this->model->filterPrivacy();
        }

        // Постраничный вывод
        $this->model->orderBy('date_pub', 'desc')->limitPage($page, $perpage);

        // Скрываем удаленные
        $this->model->filterIsNull('is_deleted');

        // Получаем количество и список записей
        $total = $this->model->getCommentsCount();
        $items = $this->model->getComments();

        $items = cmsEventsManager::hook("comments_before_list", $items);

        return cmsTemplate::getInstance()->renderInternal($this, 'list_index', array(
            'filters'        => array(),
            'dataset_name'   => $dataset_name,
            'page_url'       => $page_url,
            'page'           => $page,
            'perpage'        => $perpage,
            'total'          => $total,
            'items'          => $items,
            'user'           => $user,
            'target_user_id' => $this->target_user_id,
        ));

    }

    public function getDatasets(){

        $user = cmsUser::getInstance();
        $datasets = array();

        // Все (новые)
        $datasets['all'] = array(
            'name' => 'all',
            'title' => LANG_COMMENTS_DS_ALL,
        );

        // Мои друзья
        if ($user->is_logged){
            $datasets['friends'] = array(
                'name' => 'friends',
                'title' => LANG_COMMENTS_DS_FRIENDS,
                'filter' => function($model){
                    $user = cmsUser::getInstance();
                    return $model->filterFriends($user->id);
                }
            );
        }

        // Только мои
        if ($user->is_logged){
            $datasets['my'] = array(
                'name' => 'my',
                'title' => LANG_COMMENTS_DS_MY,
                'filter' => function($model){
                    $user = cmsUser::getInstance();
                    return $model->filterEqual('user_id', $user->id);
                }
            );
        }

        return $datasets;

    }

//============================================================================//
//============================================================================//

}
