<?php

class actionGroupsGroupApprove extends cmsAction {

    public $lock_explicit_call = true;

    public function run($group){

        if(!$group['access']['is_moderator']){
            cmsCore::error404();
        }

        if ($group['is_approved']){ cmsCore::error404(); }

        $this->model->approveGroup($group['id'], $this->cms_user->id);

        $group['page_url'] = href_to_abs('groups', $group['slug']);

        $this->controller_moderation->approve('groups', $group);

        cmsUser::addSessionMessage(LANG_MODERATION_APPROVED, 'success');

        $this->redirectToAction($group['slug']);

    }

}
