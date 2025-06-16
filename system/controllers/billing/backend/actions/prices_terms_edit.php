<?php

class actionBillingPricesTermsEdit extends cmsAction {

    public function run($id = false) {

        return $this->runExternalAction('prices_terms_add', $this->params);
    }

}
