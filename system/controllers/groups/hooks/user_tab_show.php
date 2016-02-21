<?php

class onGroupsUserTabShow extends cmsAction {

    public function run($profile, $tab_name, $tab){

        $user = cmsUser::getInstance();
        $template = cmsTemplate::getInstance();

        $this->model->filterByMember($profile['id']);

        $page_url = href_to('users', $profile['id'], 'groups');

        $list_html = $this->renderGroupsList($page_url, 'popular');

        return $template->renderInternal($this, 'profile_tab', array(
            'user'    => $user,
            'tab'     => $tab,
            'profile' => $profile,
            'html'    => $list_html
        ));

    }

}
