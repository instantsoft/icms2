<?php
/**
 * @property \modelBilling $model
 * @property \modelUsers $model_users
 * @property \modelContent $model_content
 * @property \messages $controller_messages
 */
class billing extends cmsFrontend {

    use icms\controllers\billing\compatibility;

    protected $useOptions = true;

    public function __construct(cmsRequest $request) {

        parent::__construct($request);

        $this->model->setControllerOptions($this->options);
    }

    public function before($action_name) {

        $this->cms_core->response->setHeader('X-Frame-Options', 'DENY');

        return parent::before($action_name);
    }

    /**
     * Переводит баллы, снимая у $from_id,начисляя $to_id
     *
     * @param float|int $amount         Сумма перевода
     * @param int $from_id              ID с кого снимать
     * @param int $to_id                ID кому переводить
     * @param ?string $from_description Описание операции списания
     * @param ?string $to_description   Описание операции пополнения
     * @return bool
     */
    public function transfer($amount, $from_id, $to_id, $from_description = null, $to_description = null) {

        $success = $this->model->decrementUserBalance($from_id, $amount, $from_description);

        $success = $success && $this->model->incrementUserBalance($to_id, $amount, $to_description);

        return $success;
    }

    /**
     * Возвращает стоимость действия и данные действия
     *
     * @param string $controller Имя контроллера
     * @param string $name       Имя действия
     * @param ?int $user_id      ID пользователя, не передан - текущий
     * @return array [$price, $action]
     */
    public function getPriceAndAction(string $controller, string $name, $user_id = null) {

        $action = $this->model->getAction($controller, $name);
        if (!$action) {
            return [0, []];
        }

        return [$this->getPriceForUser($action['prices'], $user_id), $action];
    }

    /**
     * Проверяет баланс перед действием
     *
     * @param string $controller Имя контроллера
     * @param string $name       Имя действия
     * @param ?string $back_url  URI для редиректа после оплаты
     * @return bool|redirect
     */
    public function checkBalanceForAction(string $controller, string $name, $back_url = null) {

        if ($this->cms_user->is_admin) {
            return true;
        }

        [$price, $action] = $this->getPriceAndAction($controller, $name, $this->cms_user->id);

        if (!$price || !$action) {
            return true;
        }

        $balance = $this->model->getUserBalance($this->cms_user->id, true);

        if ($price <= $balance['total'] || $price < 0) {

            $message = $price > 0 ? LANG_BILLING_ACTION_PRICE_NOTICE : LANG_BILLING_ACTION_BONUS_NOTICE;

            cmsUser::addSessionMessage(sprintf($message, html_spellcount(abs($price), $this->options['currency'])));

            return true;
        }

        if ($balance['hold_amount'] != 0) {
            cmsUser::addSessionMessage(LANG_BILLING_ACTION_PRICE_DEBT, 'error');
        }

        cmsUser::sessionSet('billing_ticket', [
            'title'       => $action['title'],
            'amount'      => $price,
            'diff_amount' => round($price - $balance['total'], 2),
            'back_url'    => $back_url ?? $this->cms_core->uri_absolute
        ]);

        return $this->redirectTo('billing', 'deposit');
    }

    /**
     * Выполняет списание за действие
     *
     * @param string $controller Имя контроллера
     * @param string $name       Имя действия
     * @param ?int $user_id      ID пользователя
     * @return type
     */
    public function processAction(string $controller, string $name, $user_id = null) {

        if (!$user_id) {
            $user_id = $this->cms_user->id;
            if ($this->cms_user->is_admin) {
                return true;
            }
        }

        [$price, $action] = $this->getPriceAndAction($controller, $name, $user_id);

        if (!$price || !$action) {
            return true;
        }

        $price = $price * -1;

        return $this->model->changeUserBalance($user_id, $price, $action['title'], $action['id']);
    }

    /**
     * Возвращает стоимость поля для текущего юзера
     *
     * @param array $paid_field Данные о продаваемом поле
     * @param array $item       Массив записи ТК
     * @return type
     */
    public function getPaidFieldPrice(array $paid_field, array $item) {
        return $paid_field['price_field'] ? round($item[$paid_field['price_field']]??0, 2) : $this->getPriceForUser($paid_field['prices']);
    }

    /**
     * Возвращает цену по прайсу в зависимости от группы юзера
     *
     * @param array $prices
     * @param ?int $user_id
     * @return int|float
     */
    public function getPriceForUser($prices, $user_id = null) {

        $price = 0;

        if (!$prices) {
            return $price;
        }

        if (!$user_id || $user_id == $this->cms_user->id) {
            $user_groups = $this->cms_user->groups;
        }

        if ($user_id && empty($user_groups)) {
            $user_groups = cmsModel::yamlToArray($this->model->getField('{users}', $user_id, 'groups') ?: '');
        }

        foreach ($user_groups as $group_id) {

            if (!isset($prices[$group_id])) {
                continue;
            }

            $group_price = (float) $prices[$group_id];

            if ($group_price < $price || $price == 0) {
                $price = $group_price;
            }
        }

        return $price;
    }

    /**
     * Выполняет перевод от пользователя к пользователю
     *
     * @param array $transfer Массив заявки на перевод
     * @return bool
     */
    public function acceptTransfer(array $transfer) {

        $success = $this->model->acceptTransfer($transfer['id']);

        $from_id = $transfer['from_id'];
        $to_id   = $transfer['to_id'];
        $amount  = $transfer['amount'];
        $desc    = $transfer['description'];

        $from = $this->model_users->getUser($from_id);
        $to   = $this->model_users->getUser($to_id);

        $from_desc = ['text' => sprintf(LANG_BILLING_TRANSFER_LOG_FROM, $to['nickname']), 'url' => href_to('users', $to_id)];
        $to_desc   = ['text' => sprintf(LANG_BILLING_TRANSFER_LOG_TO, $from['nickname']), 'url' => href_to('users', $from_id)];

        if ($desc) {
            $from_desc['text'] .= ": {$desc}";
            $to_desc['text']   .= ": {$desc}";
        }

        $success = $success && $this->model->decrementUserBalance($from_id, $amount, $from_desc);
        $success = $success && $this->model->incrementUserBalance($to_id, $amount, $to_desc);

        if ($success && $this->options['is_transfers_notify']) {

            $letter = ['name' => 'billing_transfer_notify'];

            $letter_data = [
                'from_name'   => $from['nickname'],
                'from_url'    => href_to_abs('users', $from['id']),
                'amount'      => html_spellcount($amount, $this->options['currency']),
                'description' => $desc ? $desc : '---',
            ];

            $recipient = ['email' => $to['email']];

            $this->controller_messages->sendEmail($recipient, $letter, $letter_data);
        }

        return $success;
    }

    /**
     * Подтверждает заявку на вывод
     *
     * @param array $out Массив заявки
     * @return bool
     */
    public function confirmOut(array $out) {

        $success = $this->model->decrementUserBalance($out['user_id'], $out['amount'], sprintf(LANG_BILLING_OUT_LOG_ENTRY, $out['id']));

        if ($success && $this->options['out_email']) {

            $user = $this->model_users->getUser($out['user_id']);

            if (!$user) {
                return false;
            }

            $letter = ['name' => 'billing_out_notify'];

            $letter_data = [
                'user_name' => $user['nickname'],
                'user_url'  => href_to_profile($user, false, true),
                'amount'    => html_spellcount($out['amount'], $this->options['currency']),
                'system'    => $out['system'],
                'purse'     => $out['purse'],
                'summ'      => "{$out['summ']} {$this->options['currency_real']}",
                'done_url'  => href_to_abs('billing', 'out_done', $out['done_code'])
            ];

            $to = ['email' => $this->options['out_email']];

            $this->controller_messages->sendEmail($to, $letter, $letter_data);
        }

        return $success && $this->model->confirmOut($out['id']);
    }

    /**
     * Возвращает стоимость публикации одного дня
     * Или дней, если $days передана
     *
     * @param array $ctype Тип контента
     * @param int $user_id ID пользователя
     * @param ?int $days   Количество дней
     * @return int
     */
    public function getTermDayPrice(array $ctype, $user_id, $days = null) {

        if ($user_id == $this->cms_user->id) {

            $is_pub_end_days = cmsUser::isAllowed($ctype['name'], 'pub_long', 'days');

        } else {

            $user = $this->model_users->getUser($user_id);
            if (!$user) {
                return false;
            }

            $perm = new cmsPermissions($user);

            $is_pub_end_days = $perm->isAllowed($ctype['name'], 'pub_long', 'days');
        }

        if (!$ctype['is_date_range'] || !$is_pub_end_days) {
            return 0;
        }

        $term = $this->model->getTermForContentType($ctype['id']);
        if (!$term) {
            return 0;
        }

        $price = $this->getPriceForUser($term['prices'], $user_id);

        if ($days !== null) {
            return $price * abs($days);
        }

        return $price;
    }

    /**
     * Подключает JS и CSS для механизма цен за публикацию одного дня
     *
     * @param array $ctype Тип контента
     * @return bool
     */
    public function includeTermChecking(array $ctype) {

        $day_price = $this->getTermDayPrice($ctype, $this->cms_user->id);
        if (!$day_price) {
            return false;
        }

        $this->cms_template->addTplJSName('billing');
        $this->cms_template->addCSS($this->cms_template->getStylesFileName('billing'));

        $lang  = $this->cms_template->getLangJS('LANG_BILLING_CP_TERM_PRICE');
        $lang .= "var CURR = '{$this->options['currency']}'; ";

        $this->cms_template->addBottom("<script>{$lang} $(function(){ icms.billing.checkPubPrice({$day_price}); });</script>");

        return true;
    }

    /**
     * Подключает JS и CSS для механизма цен за заполнение полей
     *
     * @param array $ctype  Тип контента
     * @param ?int $item_id ID записи
     * @return bool
     */
    public function includeVipFields(array $ctype, $item_id = null) {

        $fields = $this->model->getContentTypeVipFields($ctype['id']);
        if (!$fields) {
            return false;
        }

        $fdata = [];

        foreach ($fields as $field) {

            $is_paid = $item_id ? $this->model->isVipFieldPurchased($this->cms_user->id, $field['id'], $item_id) : false;
            if ($is_paid) {
                continue;
            }

            $price = $this->getPriceForUser($field['prices']);
            $title = LANG_BILLING_TERM_PRICE;
            $price_spell = html_spellcount(abs($price), $this->options['currency']);

            // Покажет сколько начислится за заполнение поля
            if ($price < 0) {
                $price_spell = '+' . $price_spell;
                $title = LANG_BILLING_TERM_PRICE_PLUS;
            }

            $fdata[$field['field']] = [
                'title' => $title,
                'price' => $price_spell
            ];
        }

        if (!$fdata) {
            return false;
        }

        $this->cms_template->addTplJSName('billing');
        $this->cms_template->addCSS($this->cms_template->getStylesFileName('billing'));

        $fdata_json = json_encode($fdata);
        $fdata_icon = html_svg_icon('solid', 'coins', 16, false);

        $this->cms_template->addBottom("<script>$(function(){ icms.billing.showFieldsPrice({$fdata_json}, '{$fdata_icon}'); });</script>");

        return true;
    }

    /**
     * Списывает баланс за публикацию контента
     *
     * @param string $ctype_name Имя типа контента
     * @param array $item        Запись
     * @param int $days          Количество дней публикации
     * @param int $user_id       ID пользователя
     * @return bool
     */
    public function termCheckout(string $ctype_name, array $item, $days, $user_id) {

        $ctype = $this->model_content->getContentTypeByName($ctype_name);
        if (!$ctype) {
            return false;
        }

        $price = $this->getTermDayPrice($ctype, $user_id, $days);
        if (!$price) {
            return true;
        }

        $description = [
            'text' => sprintf(LANG_BILLING_TERM_LOG, $item['title'], html_spellcount($days, LANG_DAY1, LANG_DAY2, LANG_DAY10)),
            'url'  => href_to($ctype['name'], $item['slug']) . '.html'
        ];

        return $this->model->decrementUserBalance($user_id, $price, $description);
    }

    /**
     * Списывает или начисляет баланс за заполнение полей
     *
     * @param string $ctype_name Имя типа контента
     * @param array $item        Запись
     * @param type $user_id      ID пользователя
     * @return bool
     */
    public function fieldCheckout(string $ctype_name, array $item, $user_id) {

        $ctype = $this->model_content->getContentTypeByName($ctype_name);
        if (!$ctype) {
            return false;
        }

        $fields = $this->model->getContentTypeVipFields($ctype['id']);
        if (!$fields) {
            return true;
        }

        $success = true;

        foreach ($fields as $field) {

            if (is_empty_value($item[$field['field']] ?? false)) {
                continue;
            }

            $is_paid = !empty($item['id']) ? $this->model->isVipFieldPurchased($user_id, $field['id'], $item['id']) : false;
            if ($is_paid) {
                continue;
            }

            $price = $this->getPriceForUser($field['prices'], $user_id);
            if ($price == 0) {
                continue;
            }

            $description = [
                'text' => $field['description'],
                'url'  => href_to($ctype_name, $item['slug']) . '.html'
            ];

            $success = $success && $this->model->changeUserBalance($user_id, ($price * -1), $description);

            $success = $success && $this->model->setVipFieldPurchased($user_id, $field['id'], $item['id']);
        }

        return $success;
    }

    /**
     * Меняет рейтинг на баллы
     *
     * @param int $user_id ID пользователя
     * @param string $mode Режим: rtp или ptr
     * @param float $amount Сумма обмена
     * @return bool
     */
    public function exchangeRating($user_id, $mode, $amount) {

        if ($mode === 'rtp') {

            $summ = round($this->options['rtp_rate'] * $amount, 2);

            $success = $this->model_users->updateUserRating($user_id, $amount * -1);

            return $success && $this->model->incrementUserBalance($user_id, $summ, LANG_BILLING_EXCHANGE_RTP_LOG);
        }

        if ($mode === 'ptr') {

            $summ = floor($this->options['ptr_rate'] * $amount);

            $success = $this->model_users->updateUserRating($user_id, $summ);

            return $success && $this->model->decrementUserBalance($user_id, $amount, LANG_BILLING_EXCHANGE_PTR_LOG);
        }

        return false;
    }

    /**
     * Выплачивает бонус рефералу за регистрацию
     *
     * @param int $user_id
     * @param int $ref_id
     * @param int $link_id
     * @return type
     */
    public function payRefRegBonus($user_id, $ref_id, $link_id) {

        $user = $this->model_users->getUser($user_id);

        $description = [
            'text'        => sprintf(LANG_BILLING_REFS_REG_LOG, $user['nickname']),
            'url'         => href_to_profile($user),
            'ref_link_id' => $link_id
        ];

        return $this->model->changeBalance('user', $ref_id, $this->options['ref_bonus'], $description);
    }

    /**
     * Возвращает объект класса метода оплаты
     *
     * @param string $system_name Имя системы
     * @return \billingPaymentSystem|null
     */
    public function getPaymentSystem(string $system_name) {

        $system = $this->model->getPaymentSystemByName($system_name);
        if (!$system) {
            return null;
        }

        cmsCore::includeFile("system/controllers/{$this->name}/systems/base.php");

        $class_file = "system/controllers/{$this->name}/systems/{$system['name']}/{$system['name']}.php";
        $class_name = 'system' . ucfirst($system['name']);

        $result = cmsCore::includeFile($class_file);
        if (!$result) {
            return null;
        }

        return new $class_name($system);
    }

}
