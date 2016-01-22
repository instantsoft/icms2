<?php

class actionUsersMigrationsDelete extends cmsAction {

    public function run($rule_id){

        if (!$rule_id) { cmsCore::error404(); }

        $this->model->deleteMigrationRule($rule_id);

        $this->redirectToAction('migrations');

    }

}