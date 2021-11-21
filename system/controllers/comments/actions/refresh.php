<?php

class actionCommentsRefresh extends cmsAction {

    public $request_params = array(
        'tc' => array(
            'default' => '',
            'rules'   => array(
                array('required'),
                array('sysname'),
                array('max_length', 32)
            )
        ),
        'ts' => array(
            'default' => '',
            'rules'   => array(
                array('required'),
                array('sysname'),
                array('max_length', 32)
            )
        ),
        'ti' => array(
            'default' => 0,
            'rules'   => array(
                array('required'),
                array('digits')
            )
        ),
        'tud' => array(
            'default' => 0,
            'rules'   => array(
                array('digits')
            )
        ),
        'timestamp' => array(
            'default' => 0,
            'rules'   => array(
                array('required'),
                array('digits')
            )
        )
    );

    public function run(){

        if (!$this->request->isAjax()){ cmsCore::error404(); }
        if (!$this->cms_user->is_logged) { cmsCore::error404(); }

        $timestamp         = $this->request->get('timestamp');
        $target_controller = $this->request->get('tc');
        $target_subject    = $this->request->get('ts');
        $target_id         = $this->request->get('ti');
        $target_user_id    = $this->request->get('tud');

        // Проверяем наличие контроллера и модели
        if (!(cmsCore::isControllerExists($target_controller) &&
                    cmsCore::isModelExists($target_controller) &&
                    cmsController::enabled($target_controller))){
            return $this->cms_template->renderJSON([
                'error' => false, 'total' => 0, 'exists' => 0
            ]);
        }

        $comments_per_request = 5;

        $this->model->
                filterEqual('target_controller', $target_controller)->
                filterEqual('target_subject', $target_subject)->
                filterEqual('target_id', $target_id)->
                filterTimestampGt('date_pub', $timestamp)->
                filterNotEqual('user_id', $this->cms_user->id)->
                orderBy('id')->
                limit($comments_per_request);

        $total_count = $this->model->getCommentsCount();

        if (!$total_count){
            return $this->cms_template->renderJSON([
                'error' => false, 'total' => 0, 'exists' => 0
            ]);
        }

        $comments = $this->model->joinCommentsRating($this->cms_user->id)->getComments($this->getCommentActions());

        // Получаем модель целевого контроллера
        $target_model = cmsCore::getModel($target_controller);

        // получаем опции, если есть
        $target_options = [];
        if(method_exists($target_model, 'getCommentsOptions')){
            $target_options = $target_model->getCommentsOptions($target_subject);
        }

        $comments_collection = [];

        $template_name = !empty($target_options['template']) ? $target_options['template'] : $this->comment_template;

        foreach($comments as $comment){
            $comments_collection[] = array(
                'id'        => $comment['id'],
                'parent_id' => $comment['parent_id'],
                'level'     => $comment['level'],
                'timestamp' => strtotime($comment['date_pub']),
                'html'      => $this->cms_template->render($template_name, array(
                    'comments'       => array($comment),
                    'target_user_id' => $target_user_id,
                    'user'           => $this->cms_user,
                    'is_levels'      => true,
                    'is_controls'    => true,
                    'is_show_target' => false
                ), new cmsRequest(array(), cmsRequest::CTX_INTERNAL))
            );
        }

        // Формируем и возвращаем результат
        return $this->cms_template->renderJSON([
            'error' => false,
            'total' => $total_count,
            'exists' => $total_count > $comments_per_request ? $total_count - $comments_per_request : 0,
            'comments' => $comments_collection
        ]);

    }

}
