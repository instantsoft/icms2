<?php
/**
 * @property \modelRating $model
 */
class actionRatingVote extends cmsAction {

    private $direction;
    private $target_id;
    private $score;

    /**
     * Флаг, что рейтинг считать как среднее арифметическое
     * @var bool
     */
    private $average_rating = true;

    public function run() {

        if (!$this->request->isAjax()) {
            return cmsCore::error404();
        }

        $this->loadRequest();

        if (!$this->isValidRequest()) {
            return $this->cms_template->renderJSON([
                'success' => false,
                'message' => LANG_ERROR
            ]);
        }

        // получаем контроллер цели
        $controller = cmsCore::getController($this->target_controller, $this->request);

        // приоритет за $score
        if (!$this->score) {

            $this->score = ($this->direction === 'down' ? -1 : 1);

            $this->average_rating = false;

        } else {

            // Проверяем флаг, что рейтинг считаем как среднее арифметическое
            if (method_exists($controller, 'isAverageRating')) {
                $this->average_rating = $controller->isAverageRating($this->target_subject, $this->target_id, $this->score);
            }

            // валидация оценки
            if (method_exists($controller, 'validate_rating_score') && !$controller->validate_rating_score($this->score)) {
                return $this->cms_template->renderJSON([
                    'success' => false,
                    'message' => LANG_ERROR
                ]);
            }
        }

        // Отключены ли отрицательные оценки
        if (!empty($this->options['disable_negative_votes']) && $this->score < 0) {
            return $this->cms_template->renderJSON([
                'success' => false,
                'message' => LANG_ERROR
            ]);
        }

        // Объединяем всю информацию о голосе
        $vote = [
            'user_id'           => $this->cms_user->id ?: null,
            'target_controller' => $this->target_controller,
            'target_subject'    => $this->target_subject,
            'target_id'         => $this->target_id,
            'score'             => $this->score,
            'ip'                => $this->cms_user->ip
        ];

        $target = $controller->model->getRatingTarget($this->target_subject, $this->target_id);

        if (!$target) {
            return $this->cms_template->renderJSON([
                'success' => false,
                'message' => LANG_ERROR
            ]);
        }

        if ($this->cms_user->is_logged) {
            if ($target['user_id'] == $this->cms_user->id || !cmsUser::isAllowed($this->target_subject, 'rate')) {
                return $this->cms_template->renderJSON([
                    'success' => false,
                    'message' => LANG_RATING_DISABLED
                ]);
            }
        }

        // Этот голос уже учитывался?
        $voted_score = $this->model->isUserVoted($vote, $this->cms_user->is_logged);
        if ($voted_score) {

            if (!$this->isAllowChangingVotes($this->target_id)) {

                return $this->cms_template->renderJSON([
                    'success'   => false,
                    'rating'    => html_signed_num($target['rating']),
                    'css_class' => html_signed_class($target['rating']) . ($this->options['is_show'] ? ' clickable' : ''),
                    'message'   => LANG_RATING_VOTED
                ]);
            }

            $vote['score'] = $voted_score*-1;

            $this->score = 0;

            return $this->cancelRating($vote, $target, $controller);
        }

        return $this->addRating($vote, $target, $controller);
    }

    private function loadRequest() {

        $this->direction = $this->request->get('direction', '');
        $this->target_id = $this->request->get('id', 0);
        $this->score     = $this->request->get('score', 0);

        $this->setContext($this->request->get('controller', ''), $this->request->get('subject', ''));
    }

    private function isValidRequest() {

        // включено ли голосование от гостей?
        if (empty($this->options['allow_guest_vote']) && !$this->cms_user->is_logged) {
            return false;
        }

        $is_valid = ($this->target_controller && $this->validate_sysname($this->target_controller) === true) &&
                ($this->target_subject && $this->validate_sysname($this->target_subject) === true) &&
                $this->target_id &&
                (($this->direction && in_array($this->direction, ['up', 'down', 'clear'])) || $this->score);

        if (!$is_valid) {
            return false;
        }

        // Проверяем наличие контроллера и модели
        if (!(cmsCore::isControllerExists($this->target_controller) &&
                cmsCore::isModelExists($this->target_controller) &&
                cmsController::enabled($this->target_controller))) {
            return false;
        }

        return true;
    }

    private function addRating($vote, $target, $controller) {

        // Добавляем голос в лог
        $this->model->addVote($vote);

        // как считать суммарный рейтинг
        if ($this->average_rating) {
            $rating = round($this->model->getTargetAverageRating($vote), 0, PHP_ROUND_HALF_DOWN);
        } else {
            $rating = intval($target['rating'] + $vote['score']);
        }

        // Обновляем суммарный рейтинг цели
        $controller->model->updateRating($this->target_subject, $this->target_id, $rating);

        // Оповещаем всех об изменении рейтинга
        cmsEventsManager::hook('rating_vote', [
            'subject' => $this->target_subject,
            'id'      => $this->target_id,
            'target'  => $target,
            'vote'    => $vote,
            'rating'  => $rating
        ]);

        // ссылка на проголосовавшего
        if ($this->cms_user->is_logged) {
            $user_link = '<a href="' . href_to_profile($this->cms_user) . '">' . $this->cms_user->nickname . '</a>';
        } else {
            $user_link = LANG_GUEST;
        }

        // уведомляем автора записи
        $this->controller_messages->addRecipient($target['user_id'])->sendNoticePM([
            'content' => sprintf(LANG_RATING_PM,
            $user_link,
            $this->direction ? string_lang('LANG_RATING_' . $this->direction) : '',
            $target['page_url'],
            $target['title'])
        ], 'rating_user_vote');

        // Ставим в сессию что мы голосовали
        cmsUser::sessionSet($this->getContextKey($this->target_id), true);

        // Собираем результат
        $result = [
            'success'         => true,
            'rating_value'    => $rating,
            'score'           => $vote['score'],
            'is_allow_change' => $this->isAllowChangingVotes($this->target_id),
            'show_info'       => !empty($this->options['is_show']),
            'rating'          => html_signed_num($rating),
            'css_class'       => html_signed_class($rating) . ($this->options['is_show'] ? ' clickable' : ''),
            'message'         => LANG_RATING_VOTED
        ];

        return $this->cms_template->renderJSON($result);
    }

    private function cancelRating($vote, $target, $controller) {

        $this->model->cancelVote($vote);

        // как считать суммарный рейтинг
        if ($this->average_rating) {
            $rating = round($this->model->getTargetAverageRating($vote), 0, PHP_ROUND_HALF_DOWN);
        } else {
            $rating = intval($target['rating'] + $vote['score']);
        }

        // Обновляем суммарный рейтинг цели
        $controller->model->updateRating($this->target_subject, $this->target_id, $rating);

        // Оповещаем всех об изменении рейтинга
        cmsEventsManager::hook('rating_vote', [
            'subject' => $this->target_subject,
            'id'      => $this->target_id,
            'target'  => $target,
            'vote'    => $vote,
            'rating'  => $rating
        ]);

        $result = [
            'success'      => true,
            'rating_value' => $rating,
            'show_info'    => !empty($this->options['is_show']),
            'rating'       => html_signed_num($rating),
            'css_class'    => html_signed_class($rating) . ($this->options['is_show'] ? ' clickable' : ''),
            'message'      => LANG_RATING_VOTED
        ];

        return $this->cms_template->renderJSON($result);
    }

}
