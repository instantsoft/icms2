<?php

class onBillingContentEdit extends cmsAction {

    public function run($data) {

        if ($this->cms_user->is_admin) {
            return $data;
        }

        list($ctype, $item) = $data;

        $this->includeTermChecking($ctype);
        $this->includeVipFields($ctype, $item['id']);

        return $data;
    }

}
