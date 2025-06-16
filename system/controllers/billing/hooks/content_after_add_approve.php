<?php
/**
 * @property \modelBilling $model
 */
class onBillingContentAfterAddApprove extends cmsAction {

    use \icms\controllers\billing\traits\processctypeitem;

    public function run($data) {

        $this->afterApproveItem($data);

        return $data;
    }

}
