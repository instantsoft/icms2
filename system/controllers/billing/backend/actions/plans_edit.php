<?php

class actionBillingPlansEdit extends cmsAction {

    public function run($id = false) {

        return $this->runExternalAction('plans_add', $this->params);
    }

}
