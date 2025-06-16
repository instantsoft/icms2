<?php

class onBillingUserProfileButtons extends cmsAction {

    public function run($data) {

        if (!$this->cms_user->is_logged) {
            return $data;
        }

        $profile_id = $data['profile']['id'];

        if ($profile_id == $this->cms_user->id) {
            return $data;
        }

        if (!$this->options['is_transfers']) {
            return $data;
        }

        $data['buttons'][] = [
            'title' => sprintf(LANG_BILLING_TRANSFER_TO_USER, $this->options['currency_title']),
            'class' => 'coins_add',
            'icon'  => 'hand-holding-usd',
            'href'  => href_to($this->name, 'transfer', $profile_id)
        ];

        return $data;
    }

}
