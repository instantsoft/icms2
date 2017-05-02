<?php

class actionGroupsGroupMembers extends cmsAction {

    public $lock_explicit_call = true;

    public function run($group){

        $users_request = new cmsRequest($this->request->getData(), cmsRequest::CTX_INTERNAL);

        $users_controller = cmsCore::getController('users', $users_request);

        $this->model->filterUsersMembers( $group['id'], $users_controller->model );

        $page_url = href_to($this->name, $group['slug'], 'members');
        $profiles_list_html = $users_controller->renderProfilesList($page_url);

        $group['sub_title'] = LANG_GROUPS_GROUP_MEMBERS;

        $this->cms_template->setPageTitle($group['sub_title'], $group['title']);
        $this->cms_template->setPageDescription($group['title'].' · '.$group['sub_title']);

        $this->cms_template->addBreadcrumb(LANG_GROUPS, href_to('groups'));
        $this->cms_template->addBreadcrumb($group['title'], href_to('groups', $group['slug']));
        $this->cms_template->addBreadcrumb($group['sub_title']);

        return $this->cms_template->render('group_members', array(
            'user'               => $this->cms_user,
            'group'              => $group,
            'profiles_list_html' => $profiles_list_html
        ));

    }

}
