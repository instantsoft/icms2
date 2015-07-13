<?php

class actionRatingVote extends cmsAction{

    public function run(){

        if (!$this->request->isAjax()){ cmsCore::error404(); }

        // Получаем параметры
        $direction = $this->request->get('direction');
        $target_controller = $this->request->get('controller');
        $target_subject = $this->request->get('subject');
        $target_id = $this->request->get('id');

        $template = cmsTemplate::getInstance();

        $is_valid = ($this->validate_sysname($target_controller)===true) &&
                    ($this->validate_sysname($target_subject)===true) &&
                    is_numeric($target_id) &&
                    in_array($direction, array('up', 'down'));	
		
        if (!$is_valid){ $template->renderJSON(array('success' => false)); }

        $user = cmsUser::getInstance();

        // Объединяем всю информацию о голосе
        $vote = array(
            'user_id' => $user->id,
            'target_controller' => $target_controller,
            'target_subject' => $target_subject,
            'target_id' => $target_id,
            'score' => $direction=='up' ? 1 : -1
        );

        // Этот голос уже учитывался?
        $is_voted = $this->model->isUserVoted($vote);
        if ($is_voted){ $template->renderJSON(array('success' => false)); }

        $target_model = cmsCore::getModel( $target_controller );

        $target = $target_model->getRatingTarget($target_subject, $target_id);

        if (!empty($target['user_id'])){
            if ($target['user_id'] == $user->id){
                $template->renderJSON(array('success' => false));
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
            'id' => $target_id,
            'target' => $target,
            'vote' => $vote,
            'rating' => $rating
        ));

        // Собираем результат
        $result = array(
            'success' => true,
            'rating' => html_signed_num( $rating ),
            'css_class' => html_signed_class( $rating ) . ($this->options['is_show'] ? ' clickable' : ''),
            'message' => LANG_RATING_VOTED
        );

        $template->renderJSON($result);

    }

}
