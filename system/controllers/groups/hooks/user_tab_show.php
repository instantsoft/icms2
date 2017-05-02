<?php

class onGroupsUserTabShow extends cmsAction {

    public function run($profile, $tab_name, $tab){

        $this->model->filterByMember($profile['id']);

        $page_url = href_to('users', $profile['id'], 'groups');

        $list_html = $this->renderGroupsList($page_url, 'popular');

        return $this->cms_template->renderInternal($this, 'profile_tab', array(
            'user'    => $this->cms_user,
            'tab'     => $tab,
            'profile' => $profile,
            'html'    => $list_html
        ));

    }

}
