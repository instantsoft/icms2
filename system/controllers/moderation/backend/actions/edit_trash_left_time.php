<?php

class actionModerationEditTrashLeftTime extends cmsAction {

    public function run($mod_id = null){

        $mod = $this->model->getTargetModeratorData($mod_id);
        if(!$mod){ cmsCore::error404(); }

		$form = $this->getForm('trash_left_time');
		if (!$form) { cmsCore::error404(); }

		if ($this->request->has('trash_left_time')){

			$trash = $form->parse($this->request, true);
			$errors = $form->validate($this, $trash);

			if (!$errors){

                $this->model->update('moderators', $mod['id'], $trash);

                if($trash['trash_left_time'] !== ''){
                    if($trash['trash_left_time']){
                        $trash_left_time = html_spellcount($trash['trash_left_time'], LANG_HOUR1, LANG_HOUR2, LANG_HOUR10);
                    } else {
                        $trash_left_time = LANG_MODERATION_TRASH_NO_REMOVE;
                    }
                } else {
                    $trash_left_time = LANG_BY_DEFAULT;
                }

                $this->cms_template->renderJSON(array(
                    'errors'          => false,
                    'id'              => $mod['user_id'],
                    'trash_left_time' => $trash_left_time,
                    'callback'        => 'leftTimeSuccess'
                ));

			}

			if ($errors){
                $this->cms_template->renderJSON(array(
                    'errors' => $errors
                ));
			}

		}

		return $this->cms_template->render('backend/trash_left_time', array(
			'errors' => (isset($errors) ? $errors : array()),
			'mod'    => $mod,
            'form'   => $form
        ));

    }

}
