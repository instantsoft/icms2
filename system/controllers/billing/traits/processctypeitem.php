<?php

namespace icms\controllers\billing\traits;

/**
 * @property \modelBilling $model
 */
trait processctypeitem {

    protected function getHoldTargetName(string $ctype_name, array $item) {
        return 'content_'.$ctype_name.'_'.$item['id'];
    }

    protected function unHoldTargetItem(string $ctype_name, array $item) {
        return $this->model->unHold($this->getHoldTargetName($ctype_name, $item), $item['user_id']);
    }

    public function afterSetItem(array $item) {

        $success = true;

        $ctype_item_price = \cmsModel::getCachedResult('billing_item_price');

        if ($ctype_item_price != 0) {

            $success = $success && $this->model->hold(
                $this->getHoldTargetName($item['ctype_name'], $item),
                $item['user_id'],
                $ctype_item_price,
                ['pub_days' => \cmsModel::getCachedResult('billing_item_pub_days') ?: 0]
            );
        }

        if (!$item['is_approved']) {
            $this->model->endTransaction($success);
        }

        return $success;
    }

    public function afterApproveItem(array $data, $is_edit = false) {

        $ctype_name = $data['ctype_name'];
        $item       = $data['item'];

        // Хук может выполняться не автором записи
        $user = $this->model_users->getUser($item['user_id']);
        if (!$user || $user['is_admin']) {
            return false;
        }

        $is_transaction_started = $this->model->isTransactionStarted();

        if (!$is_transaction_started) {

            $this->model->startTransaction();

            $balance = $this->model->forUpdate()->getUserBalance($item['user_id']);
        }

        $hold_target = $this->getHoldTargetName($ctype_name, $item);

        $hold = $this->model->getHold($hold_target, $item['user_id']);

        $success = true;

        if (!$is_edit) {
            $success = $success && $this->processAction('content', "{$ctype_name}_add", $item['user_id']);
        }

        $success = $success && $this->fieldCheckout($ctype_name, $item, $item['user_id']);

        $success = $success && $this->termCheckout($ctype_name, $item, ($hold['payload']['pub_days'] ?? 0), $item['user_id']);

        $success = $success && $this->model->unHold($hold_target, $item['user_id']);

        $this->model->endTransaction($success);

        return $success;
    }

}
