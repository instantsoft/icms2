<?php

class actionGroupsGroupMembers extends cmsAction {

    public $lock_explicit_call = true;

    public function run($group){

        $users_request = new cmsRequest($this->request->getData(), cmsRequest::CTX_INTERNAL);

        $users_controller = cmsCore::getController('users', $users_request);

        $this->model->filterUsersMembers( $group['id'], $users_controller->model );

        $page_url = href_to($this->name, $group['id'], 'members');
        $profiles_list_html = $users_controller->renderProfilesList($page_url);

        return cmsTemplate::getInstance()->render('group_members', array(
            'user'               => $this->cms_user,
            'group'              => $group,
            'profiles_list_html' => $profiles_list_html
        ));

    }

}