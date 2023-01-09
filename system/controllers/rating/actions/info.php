<?php
/**
 * @property \modelRating $model
 */
class actionRatingInfo extends cmsAction {

    public function run() {

        if (!$this->request->isAjax()) {
            return cmsCore::error404();
        }
        if (!$this->options['is_show']) {
            return cmsCore::error404();
        }

        // Получаем параметры
        $target_controller = $this->request->get('controller', '');
        $target_subject    = $this->request->get('subject', '');
        $target_id         = $this->request->get('id', 0);

        // Флаг что нужно вывести только голый список
        $is_list_only = $this->request->get('is_list_only');

        $page    = $this->request->get('page', 1);
        $perpage = 10;

        $this->model->filterVotes($target_controller, $target_subject, $target_id)->
                orderBy('id', 'desc')->
                limitPage($page, $perpage);

        $total = $this->model->getVotesCount();
        $votes = $this->model->getVotes();

        $pages = ceil($total / $perpage);

        if ($is_list_only) {

            return $this->cms_template->render('info_list', [
                'votes' => $votes,
                'user'  => $this->cms_user
            ]);
        }

        return $this->cms_template->render('info', [
            'target_controller' => $target_controller,
            'target_subject'    => $target_subject,
            'target_id'         => $target_id,
            'votes'             => $votes,
            'user'              => $this->cms_user,
            'page'              => $page,
            'pages'             => $pages,
            'perpage'           => $perpage
        ]);
    }

}
