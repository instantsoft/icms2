<?php
/**
 * @property \modelBilling $model
 */
class onBillingContentAfterAdd extends cmsAction {

    use \icms\controllers\billing\traits\processctypeitem;

    public function run($item) {

        if ($this->cms_user->is_admin) {
            return $item;
        }

        $this->afterSetItem($item);

        return $item;
    }

}
