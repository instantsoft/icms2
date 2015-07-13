<?php

class backendUsers extends cmsBackend{

    public $useDefaultOptionsAction = true;
    public $useDefaultPermissionsAction = true;

    public function actionIndex(){
        $this->redirectToAction('fields');
    }

    public function getBackendMenu(){
        return array(
            array(
                'title' => LANG_USERS_CFG_FIELDS,
                'url' => href_to($this->root_url, 'fields')
            ),
            array(
                'title' => LANG_USERS_CFG_TABS,
                'url' => href_to($this->root_url, 'tabs')
            ),
            array(
                'title' => LANG_OPTIONS,
                'url' => href_to($this->root_url, 'options')
            ),
            array(
                'title' => LANG_PERMISSIONS,
                'url' => href_to($this->root_url, 'perms', 'users')
            ),
            array(
                'title' => LANG_USERS_CFG_MIGRATION,
                'url' => href_to($this->root_url, 'migrations')
            )
        );
    }

    public function validate_unique_field($value){
        $core = cmsCore::getInstance();
        $table_name = '{users}';
        return !$core->db->isFieldExists($table_name, $value);
    }

}
