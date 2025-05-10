<?php

class onUsersMenuUsers extends cmsAction {

    public function run($item) {

        $profile = cmsModel::getCachedResult('current_profile');

        // Табы
        if ($profile && strpos($item['action'], 'profile_') === 0) {

            $action = str_replace('profile_', '', $item['action']);

            $menus = $this->getProfileMenu($profile);

            if (!isset($menus[$action])) {
                return false;
            }

            $menu = $menus[$action];

            unset($menu['title']);

            return $menu;
        }

        if (!$this->cms_user->is_logged) {
            return false;
        }

        switch ($item['action']) {
            case 'profile':

                return [
                    'url'   => href_to_profile($this->cms_user),
                    'items' => false
                ];

            case 'logout':

                if (!$profile || $profile['id'] != $this->cms_user->id) {
                    return false;
                }

                return [
                    'url'   => href_to('auth', 'logout') . '?csrf_token=' . cmsForm::getCSRFToken(),
                    'items' => false
                ];

            case 'edit':

                if (!$profile || $profile['id'] != $this->cms_user->id) {
                    return false;
                }

                return [
                    'url'   => href_to_profile($this->cms_user, ['edit']),
                    'items' => false
                ];

            case 'settings':

                return [
                    'url'   => href_to_profile($this->cms_user, ['edit']),
                    'items' => false
                ];

            case 'subscribers':

                if (!$this->cms_user->subscribers_count) {
                    return false;
                }

                return [
                    'url'     => href_to_profile($this->cms_user, ['subscribers']),
                    'counter' => $this->cms_user->subscribers_count
                ];

            case 'subscriptions':

                $this->model->filterEqual('user_id', $this->cms_user->id);

                $subscriptions_count = $this->model->getCount('subscriptions_bind');

                $this->model->resetFilters();

                if (!$subscriptions_count) {
                    return false;
                }

                return [
                    'url'     => href_to_profile($this->cms_user, ['subscriptions']),
                    'counter' => $subscriptions_count
                ];

            default:
                break;
        }

        return false;
    }

}
