<?php

class actionGroupsGroupEditRequests extends cmsAction {

    public $lock_explicit_call = true;

    public function run($group) {

        if ($group['join_policy'] == groups::JOIN_POLICY_FREE) {
            return cmsCore::error404();
        }

        if ($group['access']['member_role'] != groups::ROLE_STAFF && !$this->cms_user->is_admin) {
            return cmsCore::error404();
        }

        $users_controller = cmsCore::getController('users', $this->request);

        $this->model->filterUsersRequests($group['id'], $users_controller->model);

        $page_url = href_to($this->name, $group['slug'], ['edit', 'requests']);

        $profiles_list_html = $users_controller->renderProfilesList($page_url, false, [
            [
                'title' => LANG_ACCEPT,
                'class' => 'ajax-request',
                'href'  => href_to('groups', 'accept_request', [$group['id'], '{id}'])
            ],
            [
                'title' => LANG_DECLINE,
                'class' => 'ajax-request',
                'href'  => href_to('groups', 'decline_request', [$group['id'], '{id}'])
            ]
        ]);

        $this->cms_template->setPageTitle(LANG_GROUPS_REQUESTS);

        $this->cms_template->addBreadcrumb(LANG_GROUPS, href_to('groups'));
        $this->cms_template->addBreadcrumb($group['title'], href_to('groups', $group['slug']));
        $this->cms_template->addBreadcrumb(LANG_GROUPS_EDIT, href_to('groups', $group['slug'], 'edit'));
        $this->cms_template->addBreadcrumb(LANG_GROUPS_REQUESTS);

        return $this->cms_template->render('group_edit_requests', [
            'profiles_list_html' => $profiles_list_html,
            'group'              => $group,
            'user'               => $this->cms_user
        ]);
    }

}
