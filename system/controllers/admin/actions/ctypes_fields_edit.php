<?php

class actionAdminCtypesFieldsEdit extends cmsAction {

    public function run($ctype_id = null, $field_id = null) {

        return $this->runExternalAction('ctypes_fields_add', $this->params);
    }

}
