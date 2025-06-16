<?php

class onBillingContentAfterUpdateApprove extends cmsAction {

    use \icms\controllers\billing\traits\processctypeitem;

    public function run($data) {

        $this->afterApproveItem($data, true);

        return $data;
    }

}
