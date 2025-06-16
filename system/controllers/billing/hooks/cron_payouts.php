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

            extract($payout);

            if ($date_last) {
                $time_last = strtotime($date_last);
                $time_now  = time();
                $days_diff = floor(($time_now - $time_last) / 60 / 60 / 24);
                if ($days_diff < $period) {
                    continue;
                }
            }

            if (!$user_id) {

                if (!in_array('0', $groups)) {
                    $this->model_users->filterGroups($groups);
                }

            } else {
                $this->model_users->filterEqual('id', $user_id);
            }

            $users = $this->model_users->limit(false)->getUsers();
            if (!$users) {
                continue;
            }

            foreach ($users as $user) {

                $is_payout = true;

                if ($is_passed) {

                    $start_time = strtotime($user['date_reg']);
                    $end_time   = time();

                    $days = round(($end_time - $start_time) / 60 / 60 / 24);

                    if ($days < $passed_days) {
                        $is_payout = false;
                    }
                }

                if ($is_rating) {
                    if ($user['rating'] < $rating) {
                        $is_payout = false;
                    }
                }

                if ($is_karma) {
                    if ($user['karma'] < $karma) {
                        $is_payout = false;
                    }
                }

                if ($is_field) {
                    if ($user[$field] != $field_value) {
                        $is_payout = false;
                    }
                }

                if (!$is_payout) {
                    continue;
                }

                $amount = $field_amount ? floatval($user[$field_amount]??0) : $amount;

                if (!$amount) {
                    continue;
                }

                $amount = round($amount, 2);

                $this->model->startTransaction();

                $success = $this->model->changeUserBalance($user['id'], $amount, $title);

                $success = $success && $this->model->updatePayoutDate($id);

                $this->model->endTransaction($success);
            }
        }
    }

}
