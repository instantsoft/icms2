<?php

class onCommentsContentAfterUpdate extends cmsAction {

    public function run($item){

        $this->model->updateTracking('content', $item['ctype_data']['name'], $item['id']);

        return $item;

    }

}
