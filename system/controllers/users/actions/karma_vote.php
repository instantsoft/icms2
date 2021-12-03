<?php

class actionUsersKarmaVote extends cmsAction {

    public function run($profile_id) {

        if (!$this->cms_user->is_logged) {
            return cmsCore::error404();
        }

        if (!$this->request->isAjax()) {
            return cmsCore::error404();
        }

        if (!$this->options['is_karma']) {
            return cmsCore::error404();
        }

        $direction = $this->request->get('direction', '');
        $comment   = $this->request->get('comment', '');

        //
        // Проверяем валидность
        //
        $is_valid = cmsUser::isAllowed('users', 'vote_karma') &&
                is_numeric($profile_id) &&
                $this->cms_user->id != $profile_id &&
                in_array($direction, array('up', 'down')) &&
                (!$this->options['is_karma_comments'] || $comment);

        if (!$is_valid) {
            return $this->cms_template->renderJSON([
                'error'   => true,
                'message' => LANG_ERROR
            ]);
        }

        $profile = $this->model->getUser($profile_id);

        if (!$profile || $profile['is_locked'] ||
                !$this->model->isUserCanVoteKarma($this->cms_user->id, $profile_id, $this->options['karma_time'])) {
            return $this->cms_template->renderJSON([
                'error'   => true,
                'message' => LANG_ERROR
            ]);
        }

        //
        // Сохраняем оценку
        //
        $vote = [
            'user_id'    => $this->cms_user->id,
            'profile_id' => $profile_id,
            'points'     => $direction == 'up' ? 1 : -1,
            'comment'    => $comment
        ];

        $vote_id = $this->model->addKarmaVote($vote);

        $value = $profile['karma'] + $vote['points'];

        cmsEventsManager::hook('users_karma_vote', [
            'profile' => $profile,
            'vote'    => $vote
        ]);

        return $this->cms_template->renderJSON([
            'error'     => $vote_id ? false : true,
            'value'     => html_signed_num($value),
            'css_class' => html_signed_class($value)
        ]);
    }

}
