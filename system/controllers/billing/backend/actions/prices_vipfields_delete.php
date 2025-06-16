<?php

class actionBillingPricesVipfieldsDelete extends cmsAction {

    use icms\traits\controllers\actions\deleteItem;

    public function __construct($controller, $params = []) {

        parent::__construct($controller, $params);

        $this->table_name  = 'billing_vip_fields';
        $this->success_url = $this->cms_template->href_to('prices', ['vipfields']);

    }

}
