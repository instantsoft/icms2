<?php
/**
 * @property \modelBackendBilling $model
 */
class actionBillingPricesUpdate extends cmsAction {

    public function run() {

        if ($this->model->updatePricesList()) {
            cmsUser::addSessionMessage(LANG_BILLING_CP_PRICES_UPDATE_DONE, 'success');
        }

        return $this->redirectBack();
    }

}
