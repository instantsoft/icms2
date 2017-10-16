<?php

class onGroupsUserTabShow extends cmsAction {

    public function run($profile, $tab_name, $tab){

        $this->model->filterByMember($profile['id']);

        $page_url = href_to('users', $profile['id'], 'groups');

        if ($this->cms_user->id == $profile['id'] || $this->cms_user->is_admin){
            $this->model->disableApprovedFilter();
        }

        $list_html = $this->renderGroupsList($page_url, 'popular');

        return $this->cms_template->renderInternal($this, 'profile_tab', array(
            'user'    => $this->cms_user,
            'tab'     => $tab,
            'profile' => $profile,
            'html'    => $list_html
        ));

    }

}
