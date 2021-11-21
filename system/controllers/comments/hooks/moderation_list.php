<?php

class onCommentsModerationList extends cmsAction {

    public function run($data) {

        list($counts, $ctype_name, $page_url, $action) = $data;

        $ctypes_list = array_keys($counts);

        $exists = array_search($this->name, $ctypes_list);
        if ($exists === false) {
            return false;
        }

        $list_html    = '';
        $is_moderator = false;

        if ($ctype_name == $ctypes_list[$exists]) {

            if ($action == 'index') {

                $this->model->filterByModeratorTask($this->cms_user->id, $ctype_name, $this->cms_user->is_admin);

                $is_moderator = true;

            } else if ($action == 'waiting_list') {

                $this->model->filterEqual('user_id', $this->cms_user->id);

                $this->model->filterByModeratorTask($this->cms_user->id, $ctype_name, true);
            }

            $this->model->disableApprovedFilter();

            $page    = $this->cms_core->request->get('page', 1);
            $perpage = (empty($this->options['limit']) ? 15 : $this->options['limit']);

            $this->model->orderBy('date_pub', 'desc');

            $this->model->limitPage($page, $perpage);

            cmsEventsManager::hook('comments_list_filter', $this->model);

            // Получаем количество и список записей
            $total = $this->model->getCommentsCount();
            $items = $this->model->getComments($this->getCommentActions(['is_moderator' => $is_moderator]));

            if (!$items && $page > 1) {
                cmsCore::error404();
            }

            $items = cmsEventsManager::hook('comments_before_list', $items);

            $list_html = $this->cms_template->renderInternal($this, 'list_moderation', [
                'page_url'     => $page_url,
                'is_moderator' => $is_moderator,
                'page'         => $page,
                'perpage'      => $perpage,
                'total'        => $total,
                'items'        => $items,
                'user'         => $this->cms_user
            ]);
        }

        return [
            'name'   => $this->name,
            'titles' => [
                $this->name => LANG_COMMENTS
            ],
            'list_html' => $list_html
        ];
    }

}
