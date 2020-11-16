<?php

class onActivityUserTabShow extends cmsAction {

    public function run($profile, $tab_name, $tab){

        if ($this->cms_user->id == $profile['id']){
            $this->model->filterFriendsAndSubscribe($profile['id']);
        } else {
            $this->model->filterEqual('user_id', $profile['id']);
        }

        $page_url = href_to_profile($profile, 'activity');

        if ($this->cms_user->id != $profile['id'] && !$this->cms_user->is_admin){
            $this->model->enableHiddenParentsFilter();
        }

        $list_html = $this->renderActivityList($page_url);

        if($profile['id'] == $this->cms_user->id){
            $tab['title'] = LANG_ACTIVITY_TAB_MY;
        }

        return $this->cms_template->renderInternal($this, 'profile_tab', array(
            'user'    => $this->cms_user,
            'tab'     => $tab,
            'profile' => $profile,
            'html'    => $list_html
        ));

    }

}