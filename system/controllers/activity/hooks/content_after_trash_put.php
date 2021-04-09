<?php

class onActivityContentAfterTrashPut extends cmsAction {

    public function run($data) {

        list($ctype_name, $item) = $data;

        $this->deleteEntry('content', "add.{$ctype_name}", $item['id']);

        return [$ctype_name, $item];
    }

}
