<?php

class modelBilling extends cmsModel {

    use icms\traits\controllers\models\transactable;

    const OP_TYPE_PAY    = 0;
    const OP_TYPE_INCOME = 1;

    const STATUS_CREATED  = 0;
    const STATUS_DONE     = 1;
    const STATUS_CANCELED = 2;

    const OUT_STATUS_CREATED   = 0;
    const OUT_STATUS_CONFIRMED = 1;
    const OUT_STATUS_DONE      = 2;
    const OUT_STATUS_CANCELED  = 3;

    protected $options = [];

    public function setControllerOptions($options) {
        $this->options = $options;
    }

    /**
     * Возвражщает сумму в реальной валюте
     * Согласно скидок пополнения
     *
     * @param string|float|int $amount
     * @return float
     */
    public function getDepositSumm($amount) {

        $summ = 0.0;

        foreach($this->options['prices'] as $opt_price){
            if ($amount >= $opt_price['amount']){
                $summ = (float) $amount * (float) $opt_price['price'];
            }
        }

        return round($summ, 2);
    }

    protected function pricesCallback($item, $model) {

        $item['prices'] = cmsModel::yamlToArray($item['prices']);

        return $item;
    }

    protected function paymentSystemsCallback($item, $model) {
        $item['options'] = cmsModel::yamlToArray($item['options']);
        return $item;
    }

    public function getPaymentSystems($is_only_enabled = true) {

        if ($is_only_enabled) {
            $this->filterEqual('is_enabled', 1);
        }

        $this->orderBy('ordering');

        return $this->get('billing_systems', [$this, 'paymentSystemsCallback'], 'name') ?: [];
    }

    public function getPaymentSystem($id, $by_field = 'id') {
        return $this->getItemByField('billing_systems', $by_field, $id, [$this, 'paymentSystemsCallback']);
    }

    public function getPaymentSystemByName($name, $is_only_enabled = true) {

        if ($is_only_enabled) {
            $this->filterEqual('is_enabled', 1);
        }

        return $this->getPaymentSystem($name, 'name');
    }

    public function addOperation($operation) {

        if (!empty($operation['url'])) {

            $lang_href = cmsCore::getLanguageHrefPrefix();

            $replace = cmsConfig::get('root') .($lang_href ? $lang_href.'/' : '');

            $operation['url'] = preg_replace('#^('.preg_quote($replace).')(.*)$#u', '$2', $operation['url']);
        }

        return $this->insert('billing_log', $operation);
    }

    public function getOperationsCount() {
        return $this->getCount('billing_log');
    }

    public function getOperations() {
        return $this->get('billing_log');
    }

    public function getOperation($id) {

        $this->select('u.email', 'user_email');

        $this->joinLeft('{users}', 'u', 'u.id = i.user_id');

        return $this->getItemById('billing_log', $id);
    }

    public function setOperationStatus($id, $status) {
        return $this->update('billing_log', $id, [
            'date_done' => null,
            'status'    => $status
        ]);
    }

    /**
     * Отменяет платёж
     *
     * @param int $operation_id ID операции из billing_log
     * @return bool
     */
    public function cancelPayment($operation_id) {

        $this->startTransaction();

        $operation = $this->forUpdate()->getOperation($operation_id);

        $success = $this->setOperationStatus($operation['id'], self::STATUS_CANCELED);

        $this->endTransaction($success);

        return $success;
    }

    /**
     * Подтверждает успешный платёж от платёжной системы
     *
     * @param int $operation_id ID операции из billing_log
     * @return bool
     */
    public function acceptPayment($operation_id) {

        $this->startTransaction();

        $operation = $this->forUpdate()->getOperation($operation_id);

        $success = $this->setOperationStatus($operation_id, self::STATUS_DONE);

        $success = $success && $this->alterUserBalance($operation['user_id'], $operation['amount']);

        // Вознаграждение за любой доход или пополнение рефералом
        if (in_array($this->options['ref_mode'], ['all', 'dep'])) {
            $success = $success && $this->payRefBonus($operation['amount'], $operation['user_id']);
        }

        if (!empty($operation['plan_id'])) {
            $success = $success && $this->activatePlanOnPayment($operation['user_id'], $operation['plan_id'], $operation['plan_period']);
        }

        $this->endTransaction($success);

        return $success;
    }

    /**
     * Включает подписку сразу после оплаты
     *
     * @param int $user_id ID пользователя
     * @param int $plan_id ID тарифного плана
     * @param int $period  Сколько дней включен план
     * @return bool
     */
    public function activatePlanOnPayment($user_id, $plan_id, $period) {

        $plan = $this->getPlan($plan_id);

        if (!isset($plan['prices'][$period])) {
            return false;
        }

        $price = $plan['prices'][$period];

        $success = $this->decrementUserBalance($user_id, $price['amount'], sprintf(LANG_BILLING_PLAN_TICKET, $plan['title']));

        if (!empty($price['cashback'])) {
            $success = $success && $this->incrementUserBalance($user_id, $price['cashback'], sprintf(LANG_BILLING_PLAN_TICKET_CASHBACK, $plan['title']));
        }

        return $success && $this->addUserPlanSubscribtion($user_id, $plan, $price);
    }

    public function getHold($target, $user_id) {

        $this->filterEqual('target', $target)->filterEqual('user_id', $user_id);

        return $this->getItem('billing_holds', function ($item, $model) {
            $item['payload'] = cmsModel::stringToArray($item['payload']);
            return $item;
        }) ?: [];
    }

    public function unHold($target, $user_id) {
        return $this->filterEqual('target', $target)->
              filterEqual('user_id', $user_id)->
              deleteFiltered('billing_holds');
    }

    public function hold($target, $user_id, $amount, $payload = null) {
        return $this->insert('billing_holds', [
            'target'  => $target,
            'user_id' => $user_id,
            'amount'  => $amount,
            'payload' => $payload
        ], true);
    }

    /**
     * Возвращает баланс пользователя с учётом холда
     *
     * @param int $id ID пользователя
     * @param bool $is_separate_hold Вернуть массив балансов: суммарный, холд и реальный
     * @return float
     */
    public function getUserBalance($id, $is_separate_hold = false) {

        $this->joinLeft('billing_holds', 'h', 'h.user_id = i.id');

        if (!$is_separate_hold) {
            $this->selectOnly('SUM(COALESCE(i.balance, 0) - COALESCE(h.amount, 0))', 'balance');
        } else {
            $this->selectOnly('i.balance')->
                   select('SUM(COALESCE(i.balance, 0) - COALESCE(h.amount, 0))', 'total')->
                   select('COALESCE(h.amount, 0)', 'hold_amount');
        }

        $user = $this->getItemById('{users}', $id, function ($item, $model) {
            foreach ($item as &$value) {
                $value = (float) $value;
            }
            return $item;
        });

        if (!$is_separate_hold) {
            return $user['balance'] ?? 0.00;
        }

        return $user ?? [];
    }

    /**
     * Увеличивает или уменьшает баланс пользователя
     *
     * @param int $id       ID пользователя
     * @param mixed $amount Сумма к изменению
     * @return bool
     */
    public function alterUserBalance($id, $amount) {

        if ($amount == 0) {
            return true;
        }

        cmsCache::getInstance()->clean('users.list');
        cmsCache::getInstance()->clean('users.user.' . $id);

        $this->filterEqual('id', $id);

        if ($amount > 0) {
            return $this->increment('{users}', 'balance', $amount);
        }

        return $this->decrement('{users}', 'balance', abs($amount));
    }

    public function incrementUserBalance($user_id, $amount, $description = false, $action_id = false) {
        return $this->changeUserBalance($user_id, abs($amount), $description, $action_id);
    }

    public function decrementUserBalance($user_id, $amount, $description = false, $action_id = false) {

        $amount = abs($amount) * -1;

        return $this->changeUserBalance($user_id, $amount, $description, $action_id);
    }

    /**
     * Изменение баланса пользователя с учётом реферальной программы
     *
     * @param int $user_id ID пользователя
     * @param mixed $amount Сумма во внутренней валюте, зачисляемая на баланс. Может быть отрицательной.
     * @param ?string|array $description Описание операции
     * @param ?int $action_id ID действия в Биллинге (id нужной записи из таблицы billing_actions)
     * @return bool
     */
    public function changeUserBalance($user_id, $amount, $description = null, $action_id = null) {

        // Вознаграждение за любой доход реферала
        if ($this->options['ref_mode'] === 'all') {
            $this->payRefBonus($amount, $user_id);
        }

        return $this->changeBalance('user', $user_id, $amount, $description, $action_id);
    }

    /**
     * Изменение баланса пользователя или группы
     *
     * @param string $mode Определяет чей баланс изменяется - пользователя или группы (user или group)
     * @param int $subject_id ID пользователя или группы (получателя)
     * @param float|string $amount Сумма во внутренней валюте, зачисляемая на баланс. Может быть отрицательной.
     * @param ?string|array $description Описание операции
     * @param ?int $action_id ID действия в Биллинге (id нужной записи из таблицы billing_actions)
     * @return bool
     */
    public function changeBalance(string $mode, $subject_id, $amount, $description = null, $action_id = null) {

        if (!$amount) {
            return false;
        }

        $amount = (string) $amount;

        $is_percent = strpos($amount, '%') !== false;

        switch ($mode) {
            case 'user':
                $users_ids[] = $subject_id;
                break;
            case 'group':

                if ($subject_id) {
                    $this->join('{users}_groups_members', 'm', "m.user_id = i.id AND m.group_id = '{$subject_id}'");
                }

                $users_ids = $this->limit(false)->selectOnly('i.id', 'id')->
                        get('{users}', function($user){
                    return $user['id'];
                });

                break;
            default:
                $users_ids = [];
                break;
        }

        if (!$users_ids) {
            return false;
        }

        $amount = (float) trim(str_replace(',', '.', str_replace([' ', '%', '+'], '', $amount)));

        $url         = null;
        $ref_link_id = null;

        if (is_array($description)) {
            $url         = $description['url'] ?? null;
            $ref_link_id = $description['ref_link_id'] ?? null;
            $description = $description['text'];
        }

        $success = true;

        $is_transaction_started = $this->isTransactionStarted();

        if (!$is_transaction_started) {
            $this->startTransaction();
        }

        foreach ($users_ids as $user_id) {

            $update_amount = $amount;

            if ($is_percent) {
                $balance       = $this->getUserBalance($user_id);
                $update_amount = round(($amount / 100) * $balance, 2);
            }

            $success = $success && $this->alterUserBalance($user_id, $update_amount);

            $default_description = $update_amount > 0 ? LANG_BILLING_OP_DEPOSIT : LANG_BILLING_OP_DECREMENT;

            $success = $success && $this->addOperation([
                'type'        => $update_amount > 0 ? self::OP_TYPE_INCOME : self::OP_TYPE_PAY,
                'date_done'   => null,
                'amount'      => $update_amount,
                'user_id'     => $user_id,
                'status'      => self::STATUS_DONE,
                'action_id'   => $action_id,
                'description' => $description ?? $default_description,
                'url'         => $url,
                'ref_link_id' => $ref_link_id
            ]);
        }

        if (!$is_transaction_started) {
            $this->endTransaction($success);
        }

        return $success;
    }

    public function addAction($action) {
        return $this->insert('billing_actions', $action);
    }

    public function updateAction($id, $action) {
        return $this->update('billing_actions', $id, $action);
    }

    public function deleteActions() {
        return $this->deleteFiltered('billing_actions');
    }

    public function getAction($controller, $name) {

        $this->filterEqual('controller', $controller);
        $this->filterEqual('name', $name);

        return $this->getItem('billing_actions', [$this, 'pricesCallback']);
    }

    public function getActions() {

        $actions = $this->get('billing_actions', [$this, 'pricesCallback']) ?: [];

        $result = [];

        foreach ($actions as $action) {
            $result[$action['controller']][] = $action;
        }

        return $result;
    }

    public function deleteTerms() {
        return $this->deleteFiltered('billing_terms');
    }

    public function getTerm($id, $by_field = 'id') {
        return $this->getItemByField('billing_terms', $by_field, $id, [$this, 'pricesCallback']);
    }

    public function getTermForContentType($ctype_id) {
        return $this->getTerm($ctype_id, 'ctype_id');
    }

/* ========================================================================== */

    protected function paidFieldCallback($item, $model) {

        $item['btn_titles'] = cmsModel::yamlToArray($item['btn_titles']);
        $item['prices'] = cmsModel::yamlToArray($item['prices']);

        return $item;
    }

    public function getPaidField($id) {
        return $this->getItemById('billing_paid_fields', $id, [$this, 'paidFieldCallback']);
    }

    public function getContentTypePaidFields($ctype_id) {

        return $this->filterEqual('ctype_id', $ctype_id)->
                    get('billing_paid_fields', [$this, 'paidFieldCallback'], 'field');
    }

    public function deletePaidFields() {
        return $this->deleteFiltered('billing_paid_fields');
    }

    public function isPaidFieldPurchased($user_id, $id, $item_id) {

        return (bool) $this->filterEqual('user_id', $user_id)->
                        filterEqual('item_id', $item_id)->
                        filterEqual('field_id', $id)->
                        getCount('billing_paid_fields_log', 'id', true);
    }

    public function setPaidFieldPurchased($user_id, $id, $item_id) {

        return $this->insert('billing_paid_fields_log', [
            'user_id'   => $user_id,
            'field_id'  => $id,
            'item_id'   => $item_id,
            'date_sold' => null
        ]);
    }

/* ========================================================================== */

    public function getVipField($id) {
        return $this->getItemById('billing_vip_fields', $id, [$this, 'pricesCallback']);
    }

    public function getContentTypeVipFields($ctype_id) {

        return $this->filterEqual('ctype_id', $ctype_id)->
                get('billing_vip_fields', [$this, 'pricesCallback'], 'field');
    }

    public function deleteVipFields() {
        return $this->deleteFiltered('billing_vip_fields');
    }

    public function isVipFieldPurchased($user_id, $id, $item_id) {
        return (bool) $this->
                        filterEqual('user_id', $user_id)->
                        filterEqual('item_id', $item_id)->
                        filterEqual('field_id', $id)->
                        getCount('billing_vip_fields_log', 'id', true);
    }

    public function setVipFieldPurchased($user_id, $id, $item_id) {

        return $this->insert('billing_vip_fields_log', [
            'user_id'   => $user_id,
            'field_id'  => $id,
            'item_id'   => $item_id,
            'date_sold' => null
        ]);
    }

/* ========================================================================== */

    protected function planCallback($item, $model) {

        $item['groups'] = cmsModel::yamlToArray($item['groups']);
        $item['prices'] = cmsModel::yamlToArray($item['prices']);

        foreach ($item['prices'] as &$price) {

            $consts = [string_lang($price['int'] . '1'), string_lang($price['int'] . '2'), string_lang($price['int'] . '10')];

            $price['spellcount'] = html_spellcount($price['length'], $consts);

            $price['price'] = $model->getDepositSumm($price['amount']);
        }

        return $item;
    }

    public function getPlans($is_only_enabled = true) {

        if ($is_only_enabled) {
            $this->filterEqual('is_enabled', 1);
        }

        $this->orderBy('ordering');

        return $this->get('billing_plans', [$this, 'planCallback']) ?: [];
    }

    public function getPlan($id) {
        return $this->getItemById('billing_plans', $id, [$this, 'planCallback']);
    }

    public function cancelPlan($id) {

        $this->startTransaction();

        $users = $this->forUpdate()->limit(false)->
                selectOnly('id')->select('plan_id')->
                filterEqual('plan_id', $id)->
                get('{users}', false, false) ?: [];

        $success = true;

        foreach ($users as $user) {

            $plan = $this->getUserCurrentPlan($user['id']);

            if ($plan) {
                $success = $success && $this->relegateUserPlan($user['id'], $plan);
            }
        }

        $success = $success && $this->filterEqual('plan_id', $id)->deleteFiltered('billing_plans_log');

        $this->endTransaction($success);

        return $success;
    }

    public function incrementPlanUsers($id) {
        return $this->filterEqual('id', $id)->increment('billing_plans', 'users');
    }

    public function decrementPlanUsers($id) {
        return $this->filterEqual('id', $id)->decrement('billing_plans', 'users');
    }

    public function getUserCurrentPlan($user_id) {

        return $this->selectOnly('p.id', 'id')->
                select('p.title', 'title')->
                select('p.description', 'description')->
                select('i.date_until', 'date_until')->
                select('i.id', 'log_id')->
                select('i.old_groups', 'old_groups')->
                join('billing_plans', 'p', 'p.id = i.plan_id')->
                filterEqual('user_id', $user_id)->
                orderBy('i.id', 'desc')->
                getItem('billing_plans_log', function ($item, $model) {

                    $item['is_remind_date_until'] = strtotime($item['date_until']) <= (time()+86400*$this->options['plan_remind_days']);
                    $item['old_groups'] = cmsModel::yamlToArray($item['old_groups']);
                    return $item;
                });
    }

    public function relegateUserPlan($user_id, $plan) {

        $success = $this->delete('billing_plans_log', $plan['log_id']);
        $success = $success && $this->decrementPlanUsers($plan['id']);

        $next_plan = $this->getUserCurrentPlan($user_id);

        if ($next_plan) {
            $success = $success && $this->incrementPlanUsers($next_plan['id']);
        }

        $success = $success && $this->update('{users}', $user_id, [
            'groups'  => $plan['old_groups'],
            'plan_id' => $next_plan['id'] ?? null
        ]);

        cmsCache::getInstance()->clean('users.list');
        cmsCache::getInstance()->clean('users.user.' . $user_id);

        return $success;
    }

    public function addUserPlanSubscribtion($user_id, $plan, $price) {

        $user = $this->selectOnly('id')->
                select('groups')->
                getItemById('{users}', $user_id, function($item, $model) {
                    $item['groups'] = cmsModel::yamlToArray($item['groups']);
                    return $item;
                });

        if (!$user) {
            return false;
        }

        $interval_len       = $price['length'];
        $interval_type      = $price['int'];
        $interval_type_code = $price['int'][0];
        $interval_prefix    = in_array($interval_type, ['HOUR', 'MINUTE']) ? 'PT' : 'P';
        $interval_string    = "{$interval_prefix}{$interval_len}{$interval_type_code}";

        $plan_interval   = new DateInterval($interval_string);
        $plan_start_date = date('Y-m-d H:i:s');
        $is_continue     = false;

        $old_logs = $this->filterEqual('user_id', $user['id'])->
                orderBy('id', 'asc')->
                get('billing_plans_log', function ($item, $model) {
                    $item['old_groups'] = cmsModel::yamlToArray($item['old_groups']);
                    return $item;
                }, false);

        $success = true;

        if ($old_logs) {

            $last_log = $old_logs[count($old_logs) - 1];

            if ($last_log['plan_id'] == $plan['id']) {
                $is_continue     = true;
                $plan_start_date = $last_log['date_until'];
            }

            foreach ($old_logs as $l) {

                $date_until = new DateTime($l['date_until']);
                $date_until->add($plan_interval);

                $success = $success && $this->update('billing_plans_log', $l['id'], [
                    'date_until' => $date_until->format('Y-m-d')
                ]);

                if (!$l['is_paused'] && (!$is_continue || $l['id'] != $last_log['id'])) {

                    $success = $success && $this->decrementPlanUsers($l['plan_id']);

                    $success = $success && $this->update('billing_plans_log', $l['id'], [
                        'is_paused' => true
                    ]);
                }
            }
        }

        $date_until = new DateTime($plan_start_date);
        $date_until->add($plan_interval);

        if (!$is_continue) {

            $success = $success && $this->insert('billing_plans_log', [
                'user_id'    => $user['id'],
                'plan_id'    => $plan['id'],
                'date_until' => $date_until->format('Y-m-d H:i:s'),
                'old_groups' => $user['groups']
            ]);

            $groups = array_unique(array_merge($user['groups'], $plan['groups']));

            $success = $success && $this->update('{users}', $user['id'], [
                'groups'  => $groups,
                'plan_id' => $plan['id']
            ]);

            $success = $success && $this->incrementPlanUsers($plan['id']);
        }

        // Вознаграждение за расход реферала на покупку подписки
        if ($success && $this->options['ref_mode'] === 'sub') {
            $success = $success && $this->payRefBonus($price['amount'], $user_id);
        }

        cmsCache::getInstance()->clean('users.list');
        cmsCache::getInstance()->clean('users.user.' . $user_id);

        return $success ? $date_until->format(cmsConfig::get('date_format') . ' H:i') : false;
    }

/* ========================================================================== */

    public function addTransfer($transfer) {
        return $this->insert('billing_transfers', $transfer, false, true);
    }

    public function getTransfer($id, $by_field = 'id') {
        return $this->getItemByField('billing_transfers', $by_field, $id);
    }

    public function getTransferByCode($code) {
        return $this->getTransfer($code, 'code');
    }

    public function acceptTransfer($id) {
        return $this->update('billing_transfers', $id, [
            'status' => self::STATUS_DONE
        ]);
    }

    public function deleteTransfer($id) {
        return $this->delete('billing_transfers', $id);
    }

/* ========================================================================== */

    public function addOut ($out) {
        return $this->insert('billing_outs', $out);
    }

    public function updateOut ($id, $out) {
        return $this->update('billing_outs', $id, $out);
    }

    public function deleteOut ($id) {
        return $this->delete('billing_outs', $id);
    }

    public function getOutsCount() {
        return $this->getCount('billing_outs');
    }

    public function getOuts() {

        $statuses = $this->getOutStatuses();

        return $this->get('billing_outs', function ($item, $model) use($statuses) {

            $item['status_text'] = $statuses[$item['status']];
            $item['show_date_done'] = in_array($item['status'], [self::OUT_STATUS_DONE, self::OUT_STATUS_CANCELED]);
            $item['can_delete'] = $item['status'] == self::OUT_STATUS_CREATED;

            return $item;
        });
    }

    public function getOut($id, $by_field = 'id') {
        return $this->getItemByField('billing_outs', $by_field, $id);
    }

    public function getOutByCode($code) {
        return $this->getOut($code, 'code');
    }

    public function getOutByDoneCode($code) {
        return $this->getOut($code, 'done_code');
    }

    public function doneOut($id) {
        return $this->update('billing_outs', $id, [
            'status'    => self::OUT_STATUS_DONE,
            'date_done' => null
        ]);
    }

    public function confirmOut($id) {
        return $this->update('billing_outs', $id, [
            'status' => self::OUT_STATUS_CONFIRMED
        ]);
    }

    public function cancelOut($out) {

        $success = $this->update('billing_outs', $out['id'], [
            'status'    => self::OUT_STATUS_CANCELED,
            'date_done' => null
        ]);

        return $success && $this->incrementUserBalance($out['user_id'], $out['amount'], sprintf(LANG_BILLING_OUT_LOG_ENTRY_CANCEL, $out['id']));
    }

    public function getOutStatuses() {
        return [
            self::OUT_STATUS_CREATED   => LANG_BILLING_OUT_STATUS_CREATED,
            self::OUT_STATUS_CONFIRMED => LANG_BILLING_OUT_STATUS_CONFIRMED,
            self::OUT_STATUS_DONE      => LANG_BILLING_OUT_STATUS_DONE,
            self::OUT_STATUS_CANCELED  => LANG_BILLING_OUT_STATUS_CANCELED
        ];
    }

    public function isUserHasOutsInPeriod($user_id, $days) {
        return (bool) $this->filterEqual('user_id', $user_id)->
                        filterDateYounger('date_created', $days)->
                        getCount('billing_outs', 'id', true);
    }

    public function isUserHasPendingOuts($user_id) {
        return (bool) $this->filterEqual('user_id', $user_id)->
                        filterNotIn('status', [self::OUT_STATUS_DONE, self::OUT_STATUS_CANCELED])->
                        getCount('billing_outs', 'id', true);
    }

/* ========================================================================== */
    /**
     * Награждает реферереров
     *
     * @param mixed $amount Сумма дохода реферала
     * @param int $user_id ID реферала
     * @param ?int $max_level Максимальный уровень пирамиды
     * @return bool
     */
    public function payRefBonus($amount, $user_id, $max_level = null) {

        if (!$this->options['is_refs'] || !$this->options['ref_levels']) {
            return true;
        }

        $user_nickname = $this->getField('{users}', $user_id, 'nickname');
        if (!$user_nickname) {
            return false;
        }

        $ancestors = $this->getReferalAncestors($user_id);
        if (!$ancestors) {
            return true;
        }

        $success = true;

        $levels = $this->options['ref_levels'];

        foreach ($ancestors as $ancestor) {

            $level_key = $ancestor['level'] - 1;

            if (!isset($levels[$level_key]['percent'])) {
                continue;
            }
            if ($max_level && ($ancestor['level'] > $max_level)) {
                continue;
            }

            $income = round($amount * ($levels[$level_key]['percent'] / 100), 2);

            if ($income <= 0) {
                continue;
            }

            $description = [
                'text'        => sprintf(LANG_BILLING_REFS_LOG, $user_nickname),
                'url'         => href_to('users', $user_id),
                'ref_link_id' => $ancestor['id']
            ];

            $success = $success && $this->changeBalance('user', $ancestor['ref_id'], $income, $description);
        }

        return $success;
    }

    /**
     * Добавляет реферала
     *
     * @param int $user_id Кто зарегистрировался
     * @param int $ref_id  По чьей ссылке
     * @return int
     */
    public function addReferal($user_id, $ref_id) {

        $link_id = $this->insert('billing_refs', [
            'user_id' => $user_id,
            'ref_id'  => $ref_id,
            'level'   => 1
        ]);

        // Кто позвал $ref_id
        $ancestors = $this->filterEqual('user_id', $ref_id)->get('billing_refs') ?: [];

        foreach ($ancestors as $a) {
            $this->insert('billing_refs', [
                'user_id' => $user_id,
                'ref_id'  => $a['ref_id'],
                'level'   => $a['level'] + 1
            ]);
        }

        return $link_id;
    }

    public function getNextReferalAncestor($ref_id, $scale = 2) {

        $ref_childs_count = $this->getUserReferalsCount($ref_id, 1);

        if ($ref_childs_count < $scale) {
            return $ref_id;
        }

        $ref_childs = $this->filterEqual('ref_id', $ref_id)->getReferals(1);

        $first_child_id = false;

        foreach ($ref_childs as $ref_child) {
            if (!$first_child_id) {
                $first_child_id = $ref_child['id'];
            }
            $ref_childs_sub_count = $this->getUserReferalsCount($ref_child['id'], 1);
            if ($ref_childs_sub_count < $scale) {
                return $ref_child['id'];
            }
        }

        return $this->getNextReferalAncestor($first_child_id, $scale);
    }

    public function getReferalAncestors($user_id) {
        return $this->filterEqual('user_id', $user_id)->get('billing_refs');
    }

    public function getReferalRootAncestorId($user_id) {

        $root = $this->filterEqual('level', 1)->
                filterEqual('user_id', $user_id)->
                getItem('billing_refs');

        if (!$root) {
            return $user_id;
        }

        return $this->getReferalRootAncestorId($root['ref_id']);
    }

    public function getReferalsCount() {
        return $this->getCount('billing_refs');
    }

    public function getUserReferalsCount($user_id, $level = false) {

        $this->filterEqual('ref_id', $user_id);

        if ($level) {
            $this->filterEqual('level', $level);
        }

        return $this->getCount('billing_refs', 'id', true);
    }

    public function getReferals($max_level = false) {

        $this->select('SUM(l.amount)', 'income_total')->
               joinLeft('billing_log', 'l', 'l.ref_link_id = i.id AND l.status = ' . self::STATUS_DONE)->
               joinUser()->groupBy('id');

        if ($max_level) {
            $this->filterLtEqual('level', $max_level);
        }

        $refs = $this->get('billing_refs', function ($item, $model) {

            $data = $model->selectOnly('SUM(i.amount)', 'income_month')->
                        filterEqual('ref_link_id', $item['id'])->
                        filterDateYounger('date_created', 30)->
                        filterEqual('status', self::STATUS_DONE)->
                        groupBy('ref_link_id')->getItem('billing_log');

            return [
                'id'           => $item['user_id'],
                'slug'         => $item['user_slug'],
                'link_id'      => $item['id'],
                'nickname'     => $item['user_nickname'],
                'level'        => $item['level'],
                'date'         => $item['date_reg'],
                'income_total' => $item['income_total'],
                'income_month' => $data['income_month'] ?? 0
            ];
        }) ?: [];

        return $refs;
    }

    public function updatePayoutDate($id) {
        return $this->update('billing_payouts', $id, ['date_last' => null]);
    }

    public function getPayouts() {
        return $this->get('billing_payouts', function ($item, $model) {
            $item['groups'] = cmsModel::yamlToArray($item['groups']);
            return $item;
        });
    }

    public function getPayout($id) {
        return $this->getItemById('billing_payouts', $id, function ($item, $model) {
            $item['groups'] = cmsModel::yamlToArray($item['groups']);
            return $item;
        });
    }

    public function deleteUser($user_id) {
        $this->filterEqual('user_id', $user_id)->deleteFiltered('billing_log');
        $this->filterEqual('user_id', $user_id)->deleteFiltered('billing_outs');
        $this->filterEqual('user_id', $user_id)->deleteFiltered('billing_paid_fields_log');
        $this->filterEqual('user_id', $user_id)->deleteFiltered('billing_payouts');
        $this->filterEqual('user_id', $user_id)->deleteFiltered('billing_plans_log');
        $this->filterEqual('user_id', $user_id)->deleteFiltered('billing_refs');
        $this->filterEqual('ref_id', $user_id)->deleteFiltered('billing_refs');
        $this->filterEqual('from_id', $user_id)->deleteFiltered('billing_transfers');
        $this->filterEqual('to_id', $user_id)->deleteFiltered('billing_transfers');
        $this->filterEqual('user_id', $user_id)->deleteFiltered('billing_vip_fields_log');
    }

}
