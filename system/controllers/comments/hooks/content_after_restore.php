<?php

class onCommentsContentAfterRestore extends cmsAction {

    public function run($data) {

        list($ctype_name, $item) = $data;

        $this->model->setCommentsIsDeleted('content', $ctype_name, $item['id'], null);

        return [$ctype_name, $item];
    }

}
