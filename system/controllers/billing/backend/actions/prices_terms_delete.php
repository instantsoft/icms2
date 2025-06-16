<?php

class actionBillingPricesTermsDelete extends cmsAction {

    use icms\traits\controllers\actions\deleteItem;

    public function __construct($controller, $params = []) {

        parent::__construct($controller, $params);

        $this->table_name  = 'billing_terms';
        $this->success_url = $this->cms_template->href_to('prices', ['terms']);

    }

}
