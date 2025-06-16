<?php

class onBillingContentAdd extends cmsAction {

    public function run($ctype) {

        if ($this->cms_user->is_admin) {
            return $ctype;
        }

        $is_submitted = $this->request->has('submit') || $this->request->has('to_draft');

        if (!$is_submitted) {
            $this->checkBalanceForAction('content', "{$ctype['name']}_add");
        }

        $this->includeTermChecking($ctype);
        $this->includeVipFields($ctype);

        return $ctype;
    }

}
