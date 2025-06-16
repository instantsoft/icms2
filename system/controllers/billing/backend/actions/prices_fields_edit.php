<?php

class actionBillingPricesFieldsEdit extends cmsAction {

    public function run($id = false) {

        return $this->runExternalAction('prices_fields_add', $this->params);
    }

}
