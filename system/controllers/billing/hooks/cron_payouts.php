<?php
/**
 * @property \modelUsers $model_users
 * @property \modelBilling $model
 * @property \messages $controller_messages
 */
class onBillingCronPayouts extends cmsAction {

    public function run() {

        $payouts = $this->model->filterEqual('is_enabled', 1)->getPayouts();
        if (!$payouts) {
            return true;
        }

        foreach ($payouts as $payout) {

            if ($payout['date_last']) {
                $time_last = strtotime($payout['date_last']);
                $time_now  = time();
                $days_diff = floor(($time_now - $time_last) / 60 / 60 / 24);
                if ($days_diff < $payout['period']) {
                    continue;
                }
            }

            if (!$payout['user_id']) {

                if ($payout['groups'] && !in_array('0', $payout['groups'])) {
                    $this->model_users->filterGroups($payout['groups']);
                }

            } else {
                $this->model_users->filterEqual('id', $payout['user_id']);
            }

            $users = $this->model_users->limit(false)->getUsers();
            if (!$users) {
                continue;
            }

            foreach ($users as $user) {

                if ($payout['is_passed']) {

                    $start_time = strtotime($user['date_reg']);
                    $end_time   = time();

                    $days = round(($end_time - $start_time) / 60 / 60 / 24);

                    if ($days < $payout['passed_days']) {
                        continue;
                    }
                }

                if ($payout['is_rating']) {
                    if ($user['rating'] < $payout['rating']) {
                        continue;
                    }
                }

                if ($payout['is_karma']) {
                    if ($user['karma'] < $payout['karma']) {
                        continue;
                    }
                }

                if ($payout['is_field']) {
                    if ($user[$payout['field']] != $payout['field_value']) {
                        continue;
                    }
                }

                $amount = $payout['field_amount'] ? (float)($user[$payout['field_amount']]??0) : (float)$payout['amount'];

                if ($amount == 0) {
                    continue;
                }

                $amount = round($amount, 2);

                if ($payout['is_topup_balance']) {

                    $user['balance'] = (float) $user['balance'];

                    if ($user['balance'] < $amount) {

                        $amount = $amount - $user['balance'];

                    } else {

                        $this->model->updatePayoutDate($payout['id']);

                        continue;
                    }
                }

                $this->model->startTransaction();

                $success = $this->model->changeUserBalance($user['id'], $amount, $payout['title']);

                $success = $success && $this->model->updatePayoutDate($payout['id']);

                $this->model->endTransaction($success);
            }
        }
    }

}
