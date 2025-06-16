<?php

class actionBillingPricesTerms extends cmsAction {

    use icms\traits\controllers\actions\listgrid;

    public function __construct($controller, $params = []) {

        parent::__construct($controller, $params);

        $this->table_name = 'billing_terms';
        $this->grid_name  = 'terms';
        $this->title      = LANG_BILLING_CP_PRICES_TERMS;

        $this->tool_buttons = [
            [
                'class' => 'add',
                'title' => LANG_BILLING_CP_TERMS_ADD,
                'href'  => $this->cms_template->href_to('prices', 'terms_add')
            ]
        ];

        $this->cms_template->addBreadcrumb(LANG_BILLING_CP_PRICES, $this->cms_template->href_to('prices'));

        $this->list_callback = function (cmsModel $model) {

            $model->select('c.title', 'ctype_title');
            $model->select('c.name', 'ctype_name');

            return $model->joinLeft('content_types', 'c', 'c.id = i.ctype_id');
        };
    }

}
