<?php

class actionGroupsFieldsEdit extends cmsAction {

    public function run($ctype_id = null, $field_id = null) {

        return $this->runExternalAction('fields_add', $this->params);
    }

}
