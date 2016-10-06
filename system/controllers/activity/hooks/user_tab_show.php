<?php

class onActivityUserTabShow extends cmsAction {

    public function run($profile, $tab_name, $tab){

        $user = cmsUser::getInstance();
        $template = cmsTemplate::getInstance();

        if ($user->id == $profile['id']){
            $this->model->filterFriends($profile['id']);
        } else {
            $this->model->filterEqual('user_id', $profile['id']);
        }

        $page_url = href_to('users', $profile['id'], 'activity');

        if ($user->id != $profile['id'] && !$user->is_admin){
            $this->model->filterHiddenParents();
        }

        $list_html = $this->renderActivityList($page_url);

        if($profile['id'] == $user->id){
            $tab['title'] = LANG_ACTIVITY_TAB_MY;
        }

        return $template->renderInternal($this, 'profile_tab', array(
            'user'    => $user,
            'tab'     => $tab,
            'profile' => $profile,
            'html'    => $list_html
        ));

    }

}