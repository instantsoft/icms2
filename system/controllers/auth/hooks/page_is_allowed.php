<?php

class onAuthPageIsAllowed extends cmsAction {

    public function run($allowed) {

        // Если сайт выключен, закрываем его от посетителей
        if (!$this->cms_config->is_site_on) {
            if (href_to('auth', 'login') !== href_to_current() && !cmsUser::isAllowed('auth', 'view_closed')) {
                return cmsCore::errorMaintenance();
            }
        }

        // Если гостям запрещено просматривать сайт, перенаправляем на страницу авторизации
        if (!empty($this->options['is_site_only_auth_users'])) {
            if (!$this->cms_user->is_logged && !in_array($this->cms_core->uri_controller, $this->options['guests_allow_controllers'])) {
                return cmsUser::goLogin();
            }
        }

        return $allowed;
    }

}
