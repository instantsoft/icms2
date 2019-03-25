<?php

class actionGroupsInviteUsers extends cmsAction {

    public function run($group_id, $dataset = null, $invite_user_id = null){

        $group = $this->model->getGroup($group_id);
        if (!$group) { cmsCore::error404(); }

        $group['access'] = $this->getGroupAccess($group);

        if (!$group['access']['is_can_invite_users']){
            cmsCore::error404();
        }

        if(is_numeric($invite_user_id)){
            return $this->sendUserInvite($group, $invite_user_id);
        }

        $users_controller = cmsCore::getController('users', $this->request);

        $this->model->filterExcludeUsersMembers($group['id'], $users_controller->model);

        $datasets = $this->getInviteUsersDatasets();

        if ($dataset && isset($datasets[$dataset])) {

            if (isset($datasets[$dataset]['filter']) && is_callable($datasets[$dataset]['filter'])){
                $this->model = $datasets[$dataset]['filter']($users_controller->model, $datasets[$dataset]);
            }

        } else if ($dataset) { cmsCore::error404(); }

        $page_url = href_to($this->name, 'invite_users', $group['id']);

        $this->cms_template->setPageTitle(LANG_GROUPS_INVITE);

        $this->cms_template->addBreadcrumb(LANG_GROUPS, href_to('groups'));
        $this->cms_template->addBreadcrumb($group['title'], href_to('groups', $group['slug']));
        $this->cms_template->addBreadcrumb(LANG_GROUPS_INVITE);

        $profiles_list_html = $users_controller->renderProfilesList($page_url, false, array(
            array(
                'title' => LANG_GROUPS_SEND_INVITE,
                'class' => 'ajax-request',
                'href'  => href_to('groups', 'invite_users', array($group['id'], 0, '{id}')),
                'handler' => function($user){
                    if (!empty($user['is_send_invite'])){
                        return false;
                    }
                    return cmsUser::getInstance()->isPrivacyAllowed($user, 'invite_group_users');
                }
            ),
            array(
                'item_css_class' => 'disable_invite',
                'handler' => function($user){
                    if (!empty($user['is_send_invite'])){
                        return false;
                    }
                    return !cmsUser::getInstance()->isPrivacyAllowed($user, 'invite_group_users');
                }
            ),
            array(
                'item_css_class' => 'invite_sended',
                'notice_title'   => LANG_GROUPS_INVITE_SENT,
                'handler' => function($user){
                    if (!empty($user['is_send_invite'])){
                        return true;
                    }
                    return false;
                }
            )
        ));

        return $this->cms_template->render('invite_users', array(
            'group'              => $group,
            'dataset'            => $dataset,
            'datasets'           => $datasets,
            'profiles_list_html' => $profiles_list_html,
        ));

    }

    private function getInviteUsersDatasets(){

        $users_options = cmsController::loadOptions('users');

        $datasets = array();

        $datasets[''] = array(
                'name' => '',
                'title' => LANG_ALL
        );

        if (!empty($users_options['is_friends_on'])){

            $datasets['friends'] = array(
                'name' => 'friends',
                'title' => LANG_USERS_FRIENDS,
                'filter' => function($model, $dset){
                    return $model->filterFriends(cmsUser::getInstance()->id);
                }
            );

        }

        return cmsEventsManager::hook('group_invite_users_datasets', $datasets);

    }

    private function sendUserInvite($group, $invite_user_id) {

        if (!$invite_user_id) { cmsCore::error404(); }

        $profile = cmsCore::getModel('users')->getUser($invite_user_id);
        if (!$profile || $profile['id'] == $this->cms_user->id) { cmsCore::error404(); }

        if (!$this->cms_user->isPrivacyAllowed($profile, 'invite_group_users')){
            cmsCore::error404();
        }

        $membership = $this->model->getMembership($group['id'], $invite_user_id);
        if ($membership) {

            return $this->cms_template->renderJSON(array(
                'errors' => true
            ));

        }

        if ($this->model->getInvite($group['id'], $invite_user_id)){

            return $this->cms_template->renderJSON(array(
                'errors' => true
            ));

        }

        return $this->sendInvite($invite_user_id, $group['id']);

    }

}
