<?php

class actionBillingPricesVipfieldsEdit extends cmsAction {

    public function run($id = false) {

        return $this->runExternalAction('prices_vipfields_add', $this->params);
    }

}
