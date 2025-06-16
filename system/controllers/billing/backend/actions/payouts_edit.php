<?php

class actionBillingPayoutsEdit extends cmsAction {

    public function run($id = false) {

        return $this->runExternalAction('payouts_add', $this->params);
    }

}
