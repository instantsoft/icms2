<?php

class actionBillingPricesTermsAdd extends cmsAction {

    use icms\traits\controllers\actions\formItem;

    public function __construct($controller, $params = []) {

        parent::__construct($controller, $params);

        $list_url = $this->cms_template->href_to('prices', 'terms');

        $this->table_name  = 'billing_terms';
        $this->form_name   = 'term';
        $this->success_url = $list_url;
        $this->title       = [
            'add'  => LANG_BILLING_CP_TERMS_ADD,
            'edit' => LANG_BILLING_CP_TERMS_EDIT
        ];

        $this->breadcrumbs = [
            [LANG_BILLING_CP_PRICES, $this->cms_template->href_to('prices')],
            [LANG_BILLING_CP_PRICES_TERMS, $list_url],
            isset($params[0]) ? LANG_BILLING_CP_TERMS_EDIT : LANG_BILLING_CP_TERMS_ADD
        ];

        $this->use_default_tool_buttons = true;
    }

}
