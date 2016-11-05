<?php

class actionGroupsGroupDelete extends cmsAction {

    public $lock_explicit_call = true;

    public function run($group){

        if (!cmsUser::isAllowed('groups', 'delete')) { cmsCore::error404(); }
        if (!cmsUser::isAllowed('groups', 'delete', 'all') && $group['owner_id'] != $this->cms_user->id) { cmsCore::error404(); }

        if ($this->request->has('submit')){

            // подтвержение получено

            $csrf_token = $this->request->get('csrf_token', '');
            $is_delete_content = $this->request->get('is_delete_content', 0);

            if (!cmsForm::validateCSRFToken($csrf_token)){ cmsCore::error404(); }

            list($group, $is_delete_content) = cmsEventsManager::hook('group_before_delete', array($group, $is_delete_content));

            $this->model->removeContentFromGroup($group['id'], $is_delete_content);

            $this->model->deleteGroup($group);

            cmsUser::addSessionMessage(sprintf(LANG_GROUPS_DELETED, $group['title']));

            $this->redirectToAction('');

        } else {

            // спрашиваем подтверждение

            return $this->cms_template->render('group_delete', array(
                'user'  => $this->cms_user,
                'group' => $group
            ));

        }

    }

}