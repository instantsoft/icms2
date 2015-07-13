<?php

class rating extends cmsFrontend {

    protected $useOptions = true;

    private $target_controller;
    private $target_subject;

    public function __construct($request){

        parent::__construct($request);

        $this->target_controller = $this->request->get('target_controller');
        $this->target_subject = $this->request->get('target_subject');

    }

    public function getWidget($target_id, $current_rating, $is_enabled=true){

        $user = cmsUser::getInstance();

        // Этот пользователь уже голосовал?
        $is_voted = $user->is_logged ? $this->model->isUserVoted(array(
            'user_id' => $user->id,
            'target_controller' => $this->target_controller,
            'target_subject' => $this->target_subject,
            'target_id' => $target_id
        )) : false;

        $template = cmsTemplate::getInstance();

        return $template->renderInternal($this, 'widget', array(
            'options' => $this->getOptions(),
            'target_controller' => $this->target_controller,
            'target_subject' => $this->target_subject,
            'target_id' => $target_id,
            'is_guest' => $user->id == 0,
            'is_enabled' => $is_enabled && !$is_voted,
            'is_voted' => $is_voted,
            'current_rating' => $current_rating ? $current_rating : 0,
            'user' => $user,
        ));

    }

}
