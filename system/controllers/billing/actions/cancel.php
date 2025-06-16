<?php

class actionBillingCancel extends cmsAction {

    public function run() {

        if (!$this->cms_user->is_logged) {
            return cmsCore::error404();
        }

        cmsUser::sessionUnset('billing_ticket');

        return $this->redirect(href_to_profile($this->cms_user, ['balance']));
    }

}
