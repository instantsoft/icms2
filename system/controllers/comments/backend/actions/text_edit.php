<?php

class actionCommentsTextEdit extends cmsAction {

    public function run($comment_id){

        if (!$this->request->isAjax()){ cmsCore::error404(); }

        $is_submit = $this->request->get('save', 0);

        $comment = $this->model->getComment($comment_id);

        $editor_params = cmsCore::getController('wysiwygs')->getEditorParams([
            'editor'  => $this->options['editor'],
            'presets' => $this->options['editor_presets']
        ]);

        if(!$is_submit){

            return $this->cms_template->render('backend/text_edit', array(
                'editor_params' => $editor_params,
                'comment' => $comment,
                'action'  => href_to($this->root_url, 'text_edit', array($comment['id']))
            ));

        }

        $csrf_token = $this->request->get('csrf_token', '');

        if (!cmsForm::validateCSRFToken($csrf_token) || !$comment){
            $this->cms_template->renderJSON(array(
                'errors' => true
            ));
        }

        $content = $this->request->get('content', '');

        // Типографируем текст
        $content_html = cmsEventsManager::hook('html_filter', [
            'text'         => $content,
            'is_auto_br'   => (!$editor_params['editor'] || $editor_params['editor'] == 'markitup'),
            'build_smiles' => $editor_params['editor'] == 'markitup'
        ]);

		if (!$content_html){
			$this->cms_template->renderJSON(array(
				'errors' => array(
                    'content' => ERR_VALIDATE_REQUIRED
                )
            ));
		}

        list($comment_id, $content, $content_html) = cmsEventsManager::hook('comment_before_update', array($comment_id, $content, $content_html));

        $this->model->updateCommentContent($comment_id, $content, $content_html);

        return $this->cms_template->renderJSON(array(
            'errors'     => false,
            'callback'   => 'successSaveComment',
            'comment_id' => $comment_id,
            'text'       => string_short($content_html, 350)
        ));

    }

}
