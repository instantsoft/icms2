<?php

class onBillingContentAfterDelete extends cmsAction {

    use \icms\controllers\billing\traits\processctypeitem;

    public function run($data) {

        $this->unHoldTargetItem($data['ctype_name'], $data['item']);

        return $data;
    }

}
