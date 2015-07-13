<?php

class actionUsersKarmaVote extends cmsAction {

    public function run($profile_id){
		
		if (!cmsUser::isLogged()) { cmsCore::error404(); }

        if (!$this->request->isAjax()){ cmsCore::error404(); }

        $user = cmsUser::getInstance();
        $direction = $this->request->get('direction');
        $comment = $this->request->get('comment');

        //
        // Проверяем валидность
        //
        $is_valid = $user->is_logged &&
                    cmsUser::isAllowed('users', 'vote_karma') &&
                    is_numeric($profile_id) &&
                    $user->id != $profile_id &&
                    in_array($direction, array('up', 'down')) &&
                    (!$this->options['is_karma_comments'] || $comment);

        if (!$is_valid){
            $result = array( 'error' => true, 'message' => LANG_ERROR );
            cmsTemplate::getInstance()->renderJSON($result);
        }

        $profile = $this->model->getUser($profile_id);

        if (!$profile || !$this->model->isUserCanVoteKarma($user->id, $profile_id, $this->options['karma_time'])){
            $result = array( 'error' => true, 'message' => LANG_ERROR );
            cmsTemplate::getInstance()->renderJSON($result);
        }

        //
        // Сохраняем оценку
        //
        $vote = array(
            'user_id' => $user->id,
            'profile_id' => $profile_id,
            'points' => $direction == 'up' ? 1 : -1,
            'comment' => $comment
        );

        $vote_id = $this->model->addKarmaVote($vote);

        $value = $profile['karma'] + $vote['points'];

        $result = array(
            'error' => $vote_id ? false : true,
            'value' => html_signed_num($value),
            'css_class' => html_signed_class($value),
        );

        cmsTemplate::getInstance()->renderJSON($result);

    }

}
