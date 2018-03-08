<?php

class actionUsersProfileSubscribers extends cmsAction {

    public $lock_explicit_call = true;

    public function run($profile){

        $tabs = $this->controller->getProfileMenu($profile);

        if (!isset($this->tabs['subscribers'])){
            cmsCore::error404();
        }

        $this->model->joinInner('{users}_friends', 'fr', 'fr.user_id = i.id');

        $this->model->filterEqual('fr.friend_id', $profile['id'])->
                filterEqual('fr.is_mutual', 0)->
                disableDeleteFilter();

        $page_url = href_to($this->name, $profile['id'], 'friends');
        $profiles_list_html = $this->renderProfilesList($page_url);

        return $this->cms_template->render('profile_friends', array(
            'user'               => $this->cms_user,
            'tabs'               => $tabs,
            'tab'                => $this->tabs['subscribers'],
            'profile'            => $profile,
            'profiles_list_html' => $profiles_list_html
        ));

    }

}
