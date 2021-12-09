<?php

class onUsersAdminDashboardBlock extends cmsAction {

    public function run($options) {

        if (!empty($options['only_titles'])) {
            return [
                'users_online' => LANG_CP_USERS_ONLINE
            ];
        }

        $dashboard_blocks = [];

        if (!array_key_exists('users_online', $options['dashboard_enabled']) || !empty($options['dashboard_enabled']['users_online'])) {

            $profiles = cmsCore::getModel('users')->filterOnlineUsers()->getUsers();
            if (!$profiles) {
                return false;
            }

            // запрещаем автоматически подключать файл css стилей контроллера
            $this->template_disable_auto_insert_css = true;

            $dashboard_blocks[] = [
                'title'   => LANG_CP_USERS_ONLINE,
                'counter' => html_spellcount(count($profiles), LANG_USERS_SPELL),
                'name'    => 'users_online',
                'html'    => $this->cms_template->renderInternal($this, 'backend/admin_dashboard_online_block', array(
                    'profiles' => $profiles
                ))
            ];
        }

        return $dashboard_blocks;
    }

}
