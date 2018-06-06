<?php

class actionGroupsSetRoles extends cmsAction {

    public function run($group_id, $user_id, $is_submit = null){

        $group = $this->model->getGroup($group_id);
        if (!$group) { cmsCore::error404(); }

        $group['access'] = $this->getGroupAccess($group);

        if (!$group['access']['is_owner'] && !$this->cms_user->is_admin) { cmsCore::error404(); }

        if (!$group['roles']) { cmsCore::error404(); }

        $membership = $this->model->getMembership($group['id'], $user_id);
        if (!$membership) { cmsCore::error404(); }

        $form = new cmsForm();

        $fieldset_id = $form->addFieldset('', 'group_options');

        $form->addField($fieldset_id, new fieldList('role_ids', array(
            'title' => LANG_GROUPS_EDIT_ROLES,
            'is_multiple' => true,
            'items' => $group['roles']
        )));

        $roles = $this->model->getUserRoles($group['id'], $user_id);

        if ($is_submit){

            $data = $form->parse($this->request, true);

            $errors = $form->validate($this,  $data);

            if (!$errors){

                $this->model->setUserRoles($group['id'], $data['role_ids'], $user_id);

                return $this->cms_template->renderJSON(array(
                    'errors'   => false,
                    'text'     => LANG_GROUPS_ROLES_SAVE,
                    'callback' => 'roleFormSuccess'
                ));

            }

            if ($errors){
                return $this->cms_template->renderJSON(array(
                    'errors' => $errors
                ));
            }


        }

        return $this->cms_template->render('set_roles', array(
            'group'   => $group,
            'data'    => array('role_ids' => $roles),
            'form'    => $form,
            'errors'  => (isset($errors) ? $errors : false),
            'user_id' => $user_id
        ));

    }

}
