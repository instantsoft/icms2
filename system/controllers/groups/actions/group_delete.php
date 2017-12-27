<?php

class actionGroupsGroupDelete extends cmsAction {

    public $lock_explicit_call = true;

    public function run($group){

        if(!$group['access']['is_can_delete']){
            cmsCore::error404();
        }

        if ($this->request->has('submit')){

            $csrf_token = $this->request->get('csrf_token', '');
            $is_delete_content = $this->request->get('is_delete_content', 0);

            if (!cmsForm::validateCSRFToken($csrf_token)){ cmsCore::error404(); }

            list($group, $is_delete_content) = cmsEventsManager::hook('group_before_delete', array($group, $is_delete_content));

            $this->model->removeContentFromGroup($group['id'], $is_delete_content);

            $this->model->deleteGroup($group);

            cmsUser::addSessionMessage(sprintf(LANG_GROUPS_DELETED, $group['title']));

            $this->redirectToAction('');

        } else {

            $this->cms_template->setPageTitle(LANG_GROUPS_DELETE);

            $this->cms_template->addBreadcrumb(LANG_GROUPS, href_to('groups'));
            $this->cms_template->addBreadcrumb($group['title'], href_to('groups', $group['slug']));
            $this->cms_template->addBreadcrumb(LANG_GROUPS_DELETE);

            return $this->cms_template->render('group_delete', array(
                'user'  => $this->cms_user,
                'group' => $group
            ));

        }

    }

}
