<?php
/**
 * @property \modelComments $model
 */
class actionCommentsTextEdit extends cmsAction {

    public function run($comment_id) {

        if (!$this->request->isAjax()) {
            return cmsCore::error404();
        }

        $is_submit = $this->request->get('save', 0);

        $comment = $this->model->getComment($comment_id);

        $editor_params = cmsCore::getController('wysiwygs')->getEditorParams([
            'editor'  => $this->options['editor'],
            'presets' => $this->options['editor_presets']
        ]);

        if (!$is_submit) {

            return $this->cms_template->render('backend/text_edit', [
                'editor_params' => $editor_params,
                'comment'       => $comment,
                'action'        => href_to($this->root_url, 'text_edit', [$comment['id']])
            ]);
        }

        $csrf_token = $this->request->get('csrf_token', '');

        if (!cmsForm::validateCSRFToken($csrf_token) || !$comment) {

            return $this->cms_template->renderJSON([
                'errors' => true
            ]);
        }

        $content = $this->request->get('content', '');

        // Типографируем текст
        $content_html = cmsEventsManager::hook('html_filter', [
            'text'         => $content,
            'typograph_id' => $this->options['typograph_id'],
            'is_auto_br'   => !$editor_params['editor'] ? true : null
        ]);

        // Типографируем исходный текст без колбэков
        $content = cmsEventsManager::hook('html_filter', [
            'text'         => $content,
            'is_process_callback' => false,
            'typograph_id' => $this->options['typograph_id'],
            'is_auto_br'   => false
        ]);

        if (!$content_html) {

            return $this->cms_template->renderJSON([
                'errors' => [
                    'content' => ERR_VALIDATE_REQUIRED
                ]
            ]);
        }

        list($comment_id, $content, $content_html) = cmsEventsManager::hook('comment_before_update', [$comment_id, $content, $content_html]);

        $this->model->updateCommentContent($comment_id, $content, $content_html);

        return $this->cms_template->renderJSON([
            'errors'     => false,
            'callback'   => 'successSaveComment',
            'comment_id' => $comment_id,
            'text'       => string_short($content_html, 350)
        ]);
    }

}
