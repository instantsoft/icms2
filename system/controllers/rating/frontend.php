<?php
/**
 * @property \modelRating $model
 */
class rating extends cmsFrontend {

    protected $useOptions = true;

    public $target_controller;
    public $target_subject;

    private $user_voted = [];
    private $total_voted = 0;
    private $label = '';

    public function __construct($request) {

        parent::__construct($request);

        $this->target_controller = $this->request->get('target_controller', '');
        $this->target_subject    = $this->request->get('target_subject', '');
    }

    public function setLabel($label) {

        $this->label = $label;

        return $this;
    }

    public function loadCurrentUserVoted($target_ids) {

        $this->user_voted = $this->model->getUserVotesTargets([
            $this->target_controller, $this->target_subject, $target_ids
        ], $this->cms_user, !empty($this->options['allow_guest_vote']));

        return $this;
    }

    public function getTotalVoted() {
        return $this->total_voted;
    }

    public function setTotalVoted($total_voted) {

        $this->total_voted = $total_voted;

        return $this;
    }

    public function loadCurrentTotalVoted($target_id) {

        $this->model->filterVotes($this->target_controller, $this->target_subject, $target_id);

        return $this->setTotalVoted($this->model->getVotesCount(true));
    }

    public function isUserVoted($target_id) {
        return $this->user_voted[$target_id] ?? false;
    }

    public function setContext(string $target_controller, string $target_subject) {

        $this->target_controller = $target_controller;
        $this->target_subject    = $target_subject;
    }

    public function getContextKey($target_id) {
        return $this->target_subject . $target_id . $this->target_controller;
    }

    public function isAllowChangingVotes($target_id) {

        if (empty($this->options['allow_changing_votes'])) {
            return false;
        }

        if (!empty($this->options['allow_changing_votes_session'])) {
            return cmsUser::sessionGet($this->getContextKey($target_id));
        }

        return true;
    }

    public function getWidget($target_id, $current_rating, $is_enabled = true) {

        // разрешено ли голосование гостям
        if(!$this->cms_user->is_logged && !empty($this->options['allow_guest_vote'])){
            $is_enabled = true;
        }

        $voted_score = $this->isUserVoted($target_id);

        $template_name = !empty($this->options['template']) ? $this->options['template'] : 'widget';

        return $this->cms_template->renderInternal($this, $template_name, [
            'show_rating'            => !($this->options['is_hidden'] && !$voted_score && ($is_enabled || !$this->cms_user->is_logged)),
            'options'                => $this->options,
            'label'                  => $this->label,
            'total_voted'            => $this->total_voted,
            'target_controller'      => $this->target_controller,
            'target_subject'         => $this->target_subject,
            'target_id'              => $target_id,
            'is_guest'               => !$this->cms_user->is_logged, // сейчас не используется, совместимость
            'is_voted'               => $voted_score, // сейчас не используется, совместимость
            'voted_score'            => $voted_score,
            'is_enabled'             => $is_enabled,
            'is_allow_vote'          => $is_enabled && !$voted_score,
            'is_allow_change'        => $this->isAllowChangingVotes($target_id),
            'disable_negative_votes' => !empty($this->options['disable_negative_votes']),
            'current_rating'         => $current_rating ? $current_rating : 0,
            'user'                   => $this->cms_user
        ]);
    }

}
