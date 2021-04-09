<?php

class onCommentsContentAfterTrashPut extends cmsAction {

    public function run($data) {

        list($ctype_name, $item) = $data;

        $this->model->setCommentsIsDeleted('content', $ctype_name, $item['id']);

        return [$ctype_name, $item];
    }

}
