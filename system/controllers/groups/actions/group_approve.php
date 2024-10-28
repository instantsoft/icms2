<?php

class actionGroupsGroupApprove extends cmsAction {

    public $lock_explicit_call = true;

    public function run($group) {

        if (!$group['access']['is_moderator']) {
            return cmsCore::error404();
        }

        if ($group['is_approved']) {
            return cmsCore::error404();
        }

        $this->model->approveGroup($group['id'], $this->cms_user->id);

        $group['page_url'] = href_to_abs('groups', $group['slug']);

        $this->controller_moderation->approve($this->name, $group);

        cmsUser::addSessionMessage(LANG_MODERATION_APPROVED, 'success');

        return $this->redirectToAction($group['slug']);
    }

}
