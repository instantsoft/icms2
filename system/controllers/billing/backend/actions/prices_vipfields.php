<?php

class actionBillingPricesVipfields extends cmsAction {

    use icms\traits\controllers\actions\listgrid;

    public function __construct($controller, $params = []) {

        parent::__construct($controller, $params);

        $this->table_name = 'billing_vip_fields';
        $this->grid_name  = 'vipfields';
        $this->title      = LANG_BILLING_CP_PRICES_VIP_FIELDS;

        $this->tool_buttons = [
            [
                'class' => 'add',
                'title' => LANG_BILLING_CP_FIELDS_ADD,
                'href'  => $this->cms_template->href_to('prices', 'vipfields_add')
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
