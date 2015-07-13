<?php

class actionGroupsGroupDelete extends cmsAction {

    public function run($group){

        $user = cmsUser::getInstance();

        if (!cmsUser::isAllowed('groups', 'delete')) { cmsCore::error404(); }
        if (!cmsUser::isAllowed('groups', 'delete', 'all') && $group['owner_id'] != $user->id) { cmsCore::error404(); }

        if ($this->request->has('submit')){

            // подтвержение получено

            $csrf_token = $this->request->get('csrf_token');
            $is_delete_content = $this->request->get('is_delete_content', false);

            if (!cmsForm::validateCSRFToken($csrf_token)){ cmsCore::error404(); }

            $this->model->removeContentFromGroup($group['id'], $is_delete_content);

            $this->model->deleteGroup($group['id']);

            cmsUser::addSessionMessage(sprintf(LANG_GROUPS_DELETED, $group['title']));

            $this->redirectToAction('');

        } else {

            // спрашиваем подтверждение

            return cmsTemplate::getInstance()->render('group_delete', array(
                'user' => $user,
                'group' => $group,
            ));

        }

    }

}
