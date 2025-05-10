<?php

class actionModerationEditTrashLeftTime extends cmsAction {

    public function run($mod_id = null) {

        $mod = $this->model->getTargetModeratorData($mod_id);
        if (!$mod) {
            return cmsCore::error404();
        }

        $form = $this->getForm('trash_left_time');
        if (!$form) {
            return cmsCore::error404();
        }

        if ($this->request->has('trash_left_time')) {

            $trash  = $form->parse($this->request, true);
            $errors = $form->validate($this, $trash);

            if (!$errors) {

                $this->model->update('moderators', $mod['id'], $trash);

                if ($trash['trash_left_time'] !== '') {
                    if ($trash['trash_left_time']) {
                        $trash_left_time = html_spellcount($trash['trash_left_time'], LANG_HOUR1, LANG_HOUR2, LANG_HOUR10);
                    } else {
                        $trash_left_time = LANG_MODERATION_TRASH_NO_REMOVE;
                    }
                } else {
                    $trash_left_time = LANG_BY_DEFAULT;
                }

                return $this->cms_template->renderJSON([
                    'errors'          => false,
                    'id'              => $mod['user_id'],
                    'trash_left_time' => $trash_left_time,
                    'callback'        => 'leftTimeSuccess'
                ]);
            }

            if ($errors) {
                return $this->cms_template->renderJSON([
                    'errors' => $errors
                ]);
            }
        }

        return $this->cms_template->render('backend/trash_left_time', [
            'errors' => $errors ?? [],
            'mod'    => $mod,
            'form'   => $form
        ]);
    }

}
