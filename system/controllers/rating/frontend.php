<?php

class rating extends cmsFrontend {

    protected $useOptions = true;

    private $target_controller;
    private $target_subject;

    public function __construct($request){

        parent::__construct($request);

        $this->target_controller = $this->request->get('target_controller', '');
        $this->target_subject    = $this->request->get('target_subject', '');

    }

    public function setContext($target_controller, $target_subject) {

        $this->target_controller = $target_controller;
        $this->target_subject    = $target_subject;

    }

    public function getWidget($target_id, $current_rating, $is_enabled=true){

        // разрешено ли голосование гостям
        if(!$this->cms_user->is_logged && !empty($this->options['allow_guest_vote'])){
            $is_enabled = true;
        }

        // эта кука ставится только если общий рейтинг не показывается до голосования
        // все проверки на стороне сервера делает экшн vote
        // т.е. просто улучшение юзабилити
        $is_voted = cmsUser::getCookie($this->target_subject.$target_id.$this->target_controller);

        return $this->cms_template->renderInternal($this, 'widget', array(
            'options'           => $this->getOptions(),
            'target_controller' => $this->target_controller,
            'target_subject'    => $this->target_subject,
            'target_id'         => $target_id,
            'is_guest'          => !$this->cms_user->is_logged,
            'is_voted'          => $is_voted,
            'is_enabled'        => ($is_voted ? false : $is_enabled),
            'current_rating'    => $current_rating ? $current_rating : 0,
            'user'              => $this->cms_user
        ));

    }

}
