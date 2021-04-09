<?php

class onActivityCtypeAfterDelete extends cmsAction {

    public function run($ctype) {

        $this->deleteType('content', "add.{$ctype['name']}");

        return $ctype;
    }

}
