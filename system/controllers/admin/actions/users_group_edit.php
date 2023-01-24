<?php

/**
 * @property \modelUsers $model_users
 */
class actionAdminUsersGroupEdit extends cmsAction {

    public function run($id = false) {

        if (!$id) {
            return cmsCore::error404();
        }

        $group = $this->model_users->localizedOff()->getGroup($id);
        if (!$group) {
            return cmsCore::error404();
        }

        $this->model_users->localizedRestore();

        $form = $this->getForm('users_group', ['edit']);

        $is_submitted = $this->request->has('submit');

        if ($is_submitted) {

            $group = $form->parse($this->request, $is_submitted);

            $errors = $form->validate($this, $group);

            if (!$errors) {

                $this->model_users->updateGroup($id, $group);

                cmsUser::addSessionMessage(LANG_CP_SAVE_SUCCESS, 'success');

                $this->redirectToAction('users');
            }

            if ($errors) {
                cmsUser::addSessionMessage(LANG_FORM_ERRORS, 'error');
            }
        }

        return $this->cms_template->render('users_group', [
            'do'     => 'edit',
            'menu'   => $this->getUserGroupsMenu('view', $group['id']),
            'group'  => $group,
            'form'   => $form,
            'errors' => isset($errors) ? $errors : false
        ]);
    }

}
