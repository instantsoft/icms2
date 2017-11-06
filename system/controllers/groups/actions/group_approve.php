<?php

class actionGroupsGroupApprove extends cmsAction {

    public $lock_explicit_call = true;

    public function run($group){

        if(!$group['access']['is_moderator']){
            cmsCore::error404();
        }

        if ($group['is_approved']){ $this->redirectBack(); }

        $task = $this->controller_moderation->model->getModeratorTask('groups', $group['id']);

        $this->model->approveGroup($group['id'], $this->cms_user->id);

        $this->controller_moderation->model->closeModeratorTask('groups', $group['id'], true, $this->cms_user->id);

        $after_action = $task['is_new_item'] ? 'add' : 'update';

        cmsEventsManager::hook("content_groups_after_{$after_action}_approve", $group);

        $group['page_url'] = href_to_abs('groups', $group['slug']);

        $this->controller_moderation->moderationNotifyAuthor($group, 'moderation_approved');

        cmsUser::addSessionMessage(LANG_MODERATION_APPROVED, 'success');

        $this->redirectToAction($group['slug']);

    }

}
