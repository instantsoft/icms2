<?php

class backendUsers extends cmsBackend {

    public $useSeoOptions     = true;
    public $useItemSeoOptions = true;
    protected $useOptions     = true;

    public $useDefaultOptionsAction     = true;
    public $useDefaultPermissionsAction = true;

    public function actionIndex() {
        $this->redirectToAction('fields');
    }

    public function getBackendMenu() {
        return [
            [
                'title' => LANG_USERS_CFG_FIELDS,
                'url'   => href_to($this->root_url, 'fields')
            ],
            [
                'title' => LANG_USERS_CFG_TABS,
                'url'   => href_to($this->root_url, 'tabs')
            ],
            [
                'title' => LANG_OPTIONS,
                'url'   => href_to($this->root_url, 'options')
            ],
            [
                'title' => LANG_PERMISSIONS,
                'url'   => href_to($this->root_url, 'perms', 'users')
            ],
            [
                'title' => LANG_USERS_CFG_MIGRATION,
                'url'   => href_to($this->root_url, 'migrations')
            ]
        ];
    }

    public function getBackendSubMenu() {

        $this->backend_sub_menu[] = [
            'title'   => LANG_USERS,
            'url'     => href_to('admin', 'users'),
            'options' => [
                'icon' => 'users'
            ]
        ];

        return $this->backend_sub_menu;
    }

    public function validate_unique_field($value) {

        if (empty($value)) {
            return true;
        }

        if (!in_array(gettype($value), ['integer', 'string'])) {
            return ERR_VALIDATE_INVALID;
        }

        $result = $this->cms_core->db->isFieldExists('{users}', $value);
        if ($result) {
            return ERR_VALIDATE_UNIQUE_FIELD;
        }

        return true;
    }

    public function getMetaItemFields() {

        $item_fields = [];

        $_item_fields = cmsCore::getModel('content')->setTablePrefix('')->orderBy('ordering')->getContentFields('{users}');

        foreach ($_item_fields as $field) {
            $item_fields[] = $field['name'];
        }

        return $item_fields;
    }

}
