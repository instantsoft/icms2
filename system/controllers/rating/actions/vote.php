<?php

class actionRatingVote extends cmsAction{

    public function run(){

        if (!$this->request->isAjax()){ cmsCore::error404(); }

        // включено ли голосование от гостей?
        if(empty($this->options['allow_guest_vote']) && !$this->cms_user->is_logged){
            return $this->cms_template->renderJSON(array(
                'success' => false,
                'message' => LANG_ERROR
            ));
        }

        // Получаем параметры
        $direction         = $this->request->get('direction', '');
        $target_controller = $this->request->get('controller', '');
        $target_subject    = $this->request->get('subject', '');
        $target_id         = $this->request->get('id', 0);

        $is_valid = ($this->validate_sysname($target_controller)===true) &&
                    ($this->validate_sysname($target_subject)===true) &&
                    is_numeric($target_id) &&
                    in_array($direction, array('up', 'down'));

        if (!$is_valid){
            return $this->cms_template->renderJSON(array(
                'success' => false,
                'message' => LANG_ERROR
            ));
        }

        // Объединяем всю информацию о голосе
        $vote = array(
            'user_id'           => ($this->cms_user->id ? $this->cms_user->id : null),
            'target_controller' => $target_controller,
            'target_subject'    => $target_subject,
            'target_id'         => $target_id,
            'score'             => ($direction == 'up' ? 1 : -1),
            'ip'                => sprintf('%u', ip2long(cmsUser::getIp()))
        );

        $cookie_key = $target_subject.$target_id.$target_controller;

        // Этот голос уже учитывался?
        $is_voted = $this->model->isUserVoted($vote, $this->cms_user->is_logged);
        if ($is_voted){
            // если куки нет, ставим
            if(!empty($this->options['is_hidden']) && !cmsUser::getCookie($cookie_key)){
                cmsUser::setCookie($cookie_key, 1, 2628000); // год
            }
            return $this->cms_template->renderJSON(array(
                'success' => false,
                'message' => LANG_RATING_VOTED
            ));
        }

        $target_model = cmsCore::getModel( $target_controller );

        $target = $target_model->getRatingTarget($target_subject, $target_id);

        if (!empty($target['user_id'])){
            if($this->cms_user->is_logged){
                if ($target['user_id'] == $this->cms_user->id || !cmsUser::isAllowed($target_subject, 'rate')){
                    return $this->cms_template->renderJSON(array(
                        'success' => false,
                        'message' => LANG_RATING_DISABLED
                    ));
                }
            }
        }

        // Добавляем голос в лог
        $this->model->addVote($vote);

        // Обновляем суммарный рейтинг цели
        $rating = (int)$target['rating'] + $vote['score'];
        $target_model->updateRating($target_subject, $target_id, $rating);

        // Оповещаем всех об изменении рейтинга
        cmsEventsManager::hook('rating_vote', array(
            'subject' => $target_subject,
            'id'      => $target_id,
            'target'  => $target,
            'vote'    => $vote,
            'rating'  => $rating
        ));

        // Собираем результат
        $result = array(
            'success'   => true,
            'rating'    => html_signed_num($rating),
            'css_class' => html_signed_class($rating) . ($this->options['is_show'] ? ' clickable' : ''),
            'message'   => LANG_RATING_VOTED
        );

        // запоминаем в куках
        if(!empty($this->options['is_hidden'])){
            cmsUser::setCookie($cookie_key, 1, 2628000); // год
        }

        return $this->cms_template->renderJSON($result);

    }

}
