<?php

class actionCommentsGet extends cmsAction {

    public function run(){

        if (!$this->request->isAjax()){ cmsCore::error404(); }
        if (!cmsUser::isAllowed('comments', 'edit')){ cmsCore::error404(); }

        $comment_id = $this->request->get('id', 0);

        if (!$comment_id){
            return $this->cms_template->renderJSON([
                'error' => true, 'message' => LANG_ERROR
            ]);
        }

        $comment = $this->model->getComment($comment_id);

        if (!$comment){
            return $this->cms_template->renderJSON([
                'error' => true, 'message' => LANG_ERROR
            ]);
        }

        if (!cmsUser::isAllowed('comments', 'edit', 'all')) {
            if (cmsUser::isAllowed('comments', 'edit', 'own') && $comment['user']['id'] != $this->cms_user->id) {
                return $this->cms_template->renderJSON([
                    'error' => true, 'message' => LANG_ERROR
                ]);
            }
        }

        $result = [
            'error' => false,
            'id'    => $comment['id'],
            'html'  => string_strip_br($comment['content'])
        ];

        list($result, $comment) = cmsEventsManager::hook('comment_before_render_json', [$result, $comment]);

        return $this->cms_template->renderJSON($result);

    }

}
