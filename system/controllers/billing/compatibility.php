<?php

namespace icms\controllers\billing;

/**
 * Трейт совместимости, можно отключить во фронтэнде
 *
 * @property \modelBilling $model
 */
trait compatibility {

    public function payRefBonus($amount, $is_deposit = false, $user_id = false, $max_level = false) {
        return $this->model->payRefBonus($amount, $user_id, $max_level);
    }

    public function incrementUserBalance($user_id, $amount, $description = false, $action_id = false) {
        return $this->model->incrementUserBalance($user_id, $amount, $description, $action_id);
    }

    public function decrementUserBalance($user_id, $amount, $description = false, $action_id = false) {
        return $this->model->decrementUserBalance($user_id, $amount, $description, $action_id);
    }

    public function changeUserBalance($user_id, $amount, $description = false, $action_id = false) {
        return $this->model->changeUserBalance($user_id, $amount, $description, $action_id);
    }

    public function changeBalance($mode, $subject_id, $amount, $description = null, $action_id = null) {
        return $this->model->changeBalance($mode, $subject_id, $amount, $description, $action_id);
    }

    public function getAction($controller, $name){
        return $this->model->getAction($controller, $name);
    }

}
