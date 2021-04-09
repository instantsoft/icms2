<?php

class onTagsContentAfterDelete extends cmsAction {

    public function run($data) {

        $ctype_name = $data['ctype_name'];
        $ctype      = $data['ctype'];
        $item       = $data['item'];

        $this->model->deleteTags('content', $ctype_name, $item['id']);

        return ['ctype_name' => $ctype_name, 'ctype' => $ctype, 'item' => $item];
    }

}
