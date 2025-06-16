<?php
/**
 * @property \modelBilling $model
 */
class onBillingUserDelete extends cmsAction {

    public function run($user) {

        $this->model->deleteUser($user['id']);

        return $user;
    }

}
