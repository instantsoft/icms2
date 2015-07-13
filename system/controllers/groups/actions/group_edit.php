<?php

class actionGroupsGroupEdit extends cmsAction {

    public function run($group, $do=false){

        if (!cmsUser::isAllowed('groups', 'edit')) { cmsCore::error404(); }

        $user = cmsUser::getInstance();

        $is_owner = $group['owner_id'] == $user->id;

        $membership = $this->model->getMembership($group['id'], $user->id);
        $is_member = ($membership !== false);
        $member_role = $is_member ? $membership['role'] : groups::ROLE_NONE;

        if (!cmsUser::isAllowed('groups', 'edit', 'all')) {
            if (cmsUser::isAllowed('groups', 'edit', 'own')) {
                if ($member_role != groups::ROLE_STAFF || ($group['edit_policy']==groups::EDIT_POLICY_OWNER && !$is_owner)){
                    cmsCore::error404();
                }
            }
        }

        // если нужно, передаем управление другому экшену
        if ($do){
            $this->runAction('group_edit_'.$do, array($group) + array_slice($this->params, 2));
            return;
        }

        $form = $this->getForm('group');

        if (!$is_owner){
            $form->removeField('basic', 'join_policy');
            $form->removeField('basic', 'edit_policy');
            $form->removeField('basic', 'wall_policy');
            $form->removeField('basic', 'is_closed');
        }

        if ($is_owner && !$this->options['is_wall']){
            $form->removeField('basic', 'wall_policy');
        }

        $is_submitted = $this->request->has('submit');

        if ($is_submitted){

            $group = array_merge($group, $form->parse($this->request, $is_submitted, $group));

            $errors = $form->validate($this, $group);

            if (!$errors){

                $this->model->updateGroup($group['id'], $group);

                $this->redirectToAction($group['id']);

            }

            if ($errors){

                cmsUser::addSessionMessage(LANG_FORM_ERRORS, 'error');

            }

        }

        return cmsTemplate::getInstance()->render('group_edit', array(
            'do' => 'edit',
            'group' => $group,
            'form' => $form,
            'errors' => isset($errors) ? $errors : false
        ));

    }

}
