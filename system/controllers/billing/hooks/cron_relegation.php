<?php
/**
 * @property \modelUsers $model_users
 * @property \modelBilling $model
 * @property \messages $controller_messages
 */
class onBillingCronRelegation extends cmsAction {

    public function run() {

        if (!$this->options['is_plans']) {
            return true;
        }

        $remind_days = $this->options['plan_remind_days'];

        $users = $this->model_users->limit(false)->filterNotNull('plan_id')->getUsers();
        if (!$users) {
            return true;
        }

        foreach ($users as $user) {

            $plan = $this->model->getUserCurrentPlan($user['id']);

            $date_until  = strtotime($plan['date_until']);
            $date_now    = time();
            $time_to_end = $date_until - $date_now;
            $days_to_end = floor($time_to_end / 60 / 60 / 24);

            $ups_key = 'plan_expired_'.$plan['id'].'_'.$user['id'];

            // Подписка истекла, отменяем
            if ($time_to_end <= 0) {
                $this->relegation($plan, $user, $ups_key);
                continue;
            }

            if ($days_to_end > $remind_days) {
                continue;
            }

            // Уведомляем о скором окончании подписки
            $this->sendRemind($plan, $user, $days_to_end, $ups_key);
        }
    }

    private function sendRemind($plan, $user, $days_to_end, $ups_key) {

        $is_send = cmsUser::getUPS($ups_key, $user['id']);

        if ($is_send) {
            return false;
        }

        cmsUser::setUPS($ups_key, 1, $user['id']);

        $letter = ['name' => 'billing_remind'];

        $letter_data = [
            'plan'       => $plan['title'],
            'days'       => html_spellcount($days_to_end, LANG_DAY1, LANG_DAY2, LANG_DAY10),
            'date_until' => html_date($plan['date_until']),
            'plan_url'   => href_to_abs($this->name, 'plan') . '?plan_id=' . $plan['id'],
        ];

        $to = ['email' => $user['email'], 'name' => $user['nickname']];

        $this->controller_messages->sendEmail($to, $letter, $letter_data);

        return true;
    }

    private function relegation($plan, $user, $ups_key) {

        $this->model->startTransaction();

        $success = $this->model->relegateUserPlan($user['id'], $plan);

        $this->model->endTransaction($success);

        if (!$success) {
            return false;
        }

        cmsUser::deleteUPS($ups_key, $user['id']);

        $letter = ['name' => 'billing_relegation'];

        $letter_data = [
            'plan'       => $plan['title'],
            'date_until' => html_date($plan['date_until']),
            'plan_url'   => href_to_abs($this->name, 'plan') . '?plan_id=' . $plan['id'],
        ];

        $to = ['email' => $user['email'], 'name' => $user['nickname']];

        $this->controller_messages->sendEmail($to, $letter, $letter_data);

        return true;
    }

}
