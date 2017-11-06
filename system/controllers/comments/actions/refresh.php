<?php

class actionCommentsRefresh extends cmsAction {

    public function run(){

        if (!$this->request->isAjax()){ cmsCore::error404(); }

        $user = cmsUser::getInstance();
        if (!$user->is_logged) { cmsCore::error404(); }

        $template = cmsTemplate::getInstance();

        $timestamp         = $this->request->get('timestamp');
        $target_controller = $this->request->get('tc');
        $target_subject    = $this->request->get('ts');
        $target_id         = $this->request->get('ti');
        $target_user_id    = $this->request->get('tud');

        $comments_per_request = 5;

        $this->model->
                filterEqual('target_controller', $target_controller)->
                filterEqual('target_subject', $target_subject)->
                filterEqual('target_id', $target_id)->
                filterTimestampGt('date_pub', $timestamp)->
                filterNotEqual('user_id', $user->id)->
                orderBy('id')->
                limit($comments_per_request);

        $total_count = $this->model->getCommentsCount();

        if (!$total_count){
            $result = array('error' => false, 'total' => 0, 'exists' => 0);
            $template->renderJSON($result);
        }

        $comments = $this->model->getComments();

        $comments_collection = array();

        $template_request = new cmsRequest(array(), cmsRequest::CTX_INTERNAL);

        foreach($comments as $comment){
            $comments_collection[] = array(
                'id'        => $comment['id'],
                'parent_id' => $comment['parent_id'],
                'level'     => $comment['level'],
                'timestamp' => strtotime($comment['date_pub']),
                'html'      => $template->render('comment', array('comments' => array($comment), 'target_user_id' => $target_user_id, 'user' => $user), $template_request)
            );
        }

        // Формируем и возвращаем результат
        $result = array(
            'error' => false,
            'total' => $total_count,
            'exists' => $total_count > $comments_per_request ? $total_count - $comments_per_request : 0,
            'comments' => $comments_collection,
        );

        $template->renderJSON($result);

    }

}
