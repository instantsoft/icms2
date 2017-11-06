<?php

class actionGroupsGroupEditRequests extends cmsAction {

    public $lock_explicit_call = true;

    public function run($group){

        if($group['join_policy'] == groups::JOIN_POLICY_FREE){ cmsCore::error404(); }

        if ($group['access']['member_role'] != groups::ROLE_STAFF && !$this->cms_user->is_admin) { cmsCore::error404(); }

        $users_controller = cmsCore::getController('users', $this->request);

        $this->model->filterUsersRequests($group['id'], $users_controller->model);

        $page_url = href_to($this->name, $group['slug'], array('edit', 'requests'));

        $profiles_list_html = $users_controller->renderProfilesList($page_url, false, array(
            array(
                'title' => LANG_ACCEPT,
                'class' => 'ajax-request',
                'href'  => href_to('groups', 'accept_request', array($group['id'], '{id}'))
            ),
            array(
                'title' => LANG_DECLINE,
                'class' => 'ajax-request',
                'href'  => href_to('groups', 'decline_request', array($group['id'], '{id}'))
            )
        ));

        $this->cms_template->setPageTitle(LANG_GROUPS_REQUESTS);

        $this->cms_template->addBreadcrumb(LANG_GROUPS, href_to('groups'));
        $this->cms_template->addBreadcrumb($group['title'], href_to('groups', $group['slug']));
        $this->cms_template->addBreadcrumb(LANG_GROUPS_EDIT, href_to('groups', $group['slug'], 'edit'));
        $this->cms_template->addBreadcrumb(LANG_GROUPS_REQUESTS);

        return $this->cms_template->render('group_edit_requests', array(
            'profiles_list_html' => $profiles_list_html,
            'group'              => $group,
            'user'               => $this->cms_user
        ));

    }

}
