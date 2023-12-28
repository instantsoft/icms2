<?php

class actionUsersProfileLock extends cmsAction {

    public $lock_explicit_call = true;

    public function run($profile) {

        if (!$this->cms_user->is_logged || !$this->request->isAjax()) {
            return cmsCore::error404();
        }

        if (!cmsUser::isAllowed('users', 'ban') || $this->is_own_profile) {
            return cmsCore::error404();
        }

        cmsCore::loadControllerLanguage('admin');

        $form = $this->getForm('lock');

        if ($this->request->has('csrf_token')) {

            $data = $form->parse($this->request, true, $profile);

            $errors = $form->validate($this, $data);

            if (!$errors) {

                if (!$data['is_locked']) {
                    $data['lock_until']  = null;
                    $data['lock_reason'] = null;
                }

                $this->model->updateUser($profile['id'], $data);

                cmsUser::addSessionMessage(LANG_SUCCESS_MSG, 'success');

                return $this->cms_template->renderJSON([
                    'errors' => false,
                    'redirect_uri' => href_to_profile($profile)
                ]);
            }

            if ($errors) {

                return $this->cms_template->renderJSON([
                    'errors' => $errors
                ]);
            }
        }

        return $this->cms_template->renderAsset('ui/typical_form', [
            'action'      => href_to_profile($profile, ['lock']),
            'data'        => $profile,
            'form'        => $form,
            'errors'      => false
        ], $this->request);
    }

}
