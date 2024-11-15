<?php
/**
 * @property \modelUsers $model_users
 */
class actionAdminUsersGroupAdd extends cmsAction {

    public function run() {

        $form = $this->getForm('users_group', ['add']);

        $is_submitted = $this->request->has('submit');

        $group = $form->parse($this->request, $is_submitted);

        if ($is_submitted) {

            $errors = $form->validate($this, $group);

            if (!$errors) {

                $id = $this->model_users->addGroup($group);

                cmsUser::addSessionMessage(sprintf(LANG_CP_USER_GROUP_CREATED, $group['title']), 'success');

                return $this->redirectToAction('users', ['group_perms', $id]);
            }

            if ($errors) {
                cmsUser::addSessionMessage(LANG_FORM_ERRORS, 'error');
            }
        }

        return $this->cms_template->render('users_group', [
            'do'     => 'add',
            'menu'   => $this->getUserGroupsMenu('add'),
            'group'  => $group,
            'form'   => $form,
            'errors' => $errors ?? false
        ]);
    }

}
