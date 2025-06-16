<?php
/**
 * @property \modelBackendBilling $model
 */
class backendBilling extends cmsBackend {

    public $useDefaultOptionsAction = true;

    protected $useOptions = true;

    public function __construct(cmsRequest $request) {

        parent::__construct($request);

        $this->model->setControllerOptions($this->options);
    }

    public function before($action_name) {

        if (!parent::before($action_name)) {
            return false;
        }

        $this->cms_core->response->setHeader('X-Frame-Options', 'DENY');

        return true;
    }

    public function getBackendMenu() {

        return [
            [
                'title' => LANG_BILLING_CP_DASHBOARD,
                'url'   => href_to($this->root_url),
                'options' => [
                    'icon' => 'tachometer-alt'
                ]
            ],
            [
                'title' => LANG_OPTIONS,
                'url'   => href_to($this->root_url, 'options'),
                'options' => [
                    'icon' => 'cog'
                ]
            ],
            [
                'title' => LANG_BILLING_CP_ADD_BAL,
                'url'   => href_to($this->root_url, 'add_balance'),
                'options' => [
                    'icon' => 'user-plus'
                ]
            ],
            [
                'title' => LANG_BILLING_CP_PRICES,
                'url'   => href_to($this->root_url, 'prices'),
                'childs_count' => 3,
                'options' => [
                    'icon' => 'money-bill-wave'
                ]
            ],
            [
                'title' => LANG_BILLING_CP_PRICES_PAID_FIELDS,
                'level' => 2,
                'url'   => href_to($this->root_url, 'prices', 'fields')
            ],
            [
                'title' => LANG_BILLING_CP_PRICES_VIP_FIELDS,
                'level' => 2,
                'url'   => href_to($this->root_url, 'prices', 'vipfields')
            ],
            [
                'title' => LANG_BILLING_CP_PRICES_TERMS,
                'level' => 2,
                'url'   => href_to($this->root_url, 'prices', 'terms')
            ],
            [
                'title' => LANG_BILLING_CP_SYSTEMS,
                'url'   => href_to($this->root_url, 'systems'),
                'options' => [
                    'icon' => 'money-check'
                ]
            ],
            [
                'title' => LANG_BILLING_CP_PLANS,
                'url'   => href_to($this->root_url, 'plans'),
                'options' => [
                    'icon' => 'handshake'
                ]
            ],
            [
                'title' => LANG_BILLING_CP_PAYOUTS,
                'url'   => href_to($this->root_url, 'payouts'),
                'options' => [
                    'icon' => 'gift'
                ]
            ],
            [
                'title' => LANG_BILLING_CP_LOG,
                'url'   => href_to($this->root_url, 'log'),
                'options' => [
                    'icon' => 'history'
                ]
            ],
            [
                'title'   => LANG_BILLING_CP_OUT,
                'counter' => $this->model->getPendingOutsCount(),
                'url'     => href_to($this->root_url, 'outs'),
                'options' => [
                    'icon' => 'tasks'
                ]
            ]
        ];
    }

    public function getPaymentSystemOptionsForm(string $system_name) {

        cmsCore::loadLanguage('controllers/' . $this->name . '/systems/' . $system_name);

        $form_file = $this->cms_config->root_path . 'system/controllers/'.$this->name.'/systems/' . $system_name . '/options.form.php';
        $form_name = implode('_', [$system_name, 'system', 'options']);

        return cmsForm::getForm($form_file, $form_name, false, $this);
    }

}
