<?php

class onBillingMenuBilling extends cmsAction {

    public function run($item) {

        if (!$this->cms_user->is_logged) {
            return false;
        }

        if ($item['action'] === 'balance') {

            return [
                'url'     => href_to_profile($this->cms_user, ['balance']),
                'counter' => $this->cms_user->balance ?: 0
            ];
        }

        return false;
    }

}
