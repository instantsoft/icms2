<?php

class actionUsersProfileTab extends cmsAction {

    public function run($profile, $tab_name){

        $user = cmsUser::getInstance();

        // Доступность профиля для данного пользователя
        if (!$user->isPrivacyAllowed($profile, 'users_profile_view')){
            cmsCore::error404();
        }

        $arguments = func_get_args();

        $tabs_menu = $this->getProfileMenu($profile);

        if (!isset($this->tabs[$tab_name]) || !$this->tabs[$tab_name]){
            cmsCore::error404();
        }

        $tab = $this->tabs[$tab_name];

        if (!isset($this->tabs_controllers[$tab['controller']])){
            cmsCore::error404();
        }

        $controller = $this->tabs_controllers[$tab['controller']];

        unset($this->tabs);
        unset($this->tabs_controllers);

        $html = $controller->runHook('user_tab_show', $arguments);

        if (!$html) { cmsCore::error404(); }

        cmsTemplate::getInstance()->render('profile_tab', array(
            'tabs' => $tabs_menu,
            'profile' => $profile,
            'user' => $user,
            'html' => $html,
        ));

    }

}
