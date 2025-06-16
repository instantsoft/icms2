<?php
/**
 * @property \modelBilling $model
 */
class onBillingCtypeAfterAdd extends cmsAction {

    public function run($ctype) {

        $this->model->addAction([
            'controller' => 'content',
            'name'       => "{$ctype['name']}_add",
            'title'      => sprintf(LANG_BILLING_ACTION_ADD_CONTENT, $ctype['title'])
        ]);

        return $ctype;
    }

}
