<?php

class billingPaymentSystem {

    /**
     * Имя платежной системы
     *
     * @var ?string
     */
    protected $name = null;

    /**
     * Заголовок платежной системы
     *
     * @var ?string
     */
    protected $title = null;

    /**
     * URL для отправки платежа
     *
     * @var ?string
     */
    protected $payment_url = null;

    /**
     * Опции платежной системы
     *
     * @var array
     */
    protected $options = [];

    /**
     * Курс валюты платежной системы
     *
     * @var float
     */
    protected $rate = 1.0;

    /**
     * ID платежной системы из БД
     *
     * @var ?int
     */
    protected $id = null;

    /**
     * Текст последней ошибки
     *
     * @var ?string
     */
    protected $last_error = null;

    public function __construct(array $system) {

        $this->id          = (int) $system['id'];
        $this->name        = $system['name'];
        $this->title       = $system['title'];
        $this->options     = array_merge($this->options, $system['options']);
        $this->rate        = (float) $system['rate'];
        $this->payment_url = $this->payment_url ?? $system['payment_url'];

        cmsCore::loadLanguage('controllers/billing/systems/' . $this->name);
    }

    /**
     * Возвращает ID платёжной системы в базе данных
     *
     * @return int
     */
    public function getDbId() {
        return $this->id;
    }

    /**
     * Возвращает название платёжной системы
     *
     * @return string
     */
    public function getTitle() {
        return $this->title;
    }

    /**
     * Возвращает опции платёжной системы
     *
     * @return array
     */
    public function getOptions() {
        return $this->options;
    }

    /**
     * Возвращает значение опции платёжной системы
     *
     * @return mixed
     */
    public function getOption(string $key) {
        return $this->options[$key] ?? '';
    }

    /**
     * Возвращает курс валюты платежной системы
     *
     * @return float
     */
    public function getRate() {
        return $this->rate;
    }

    /**
     * Возвращает URL для отправки платежа
     *
     * @return string
     */
    public function getPaymentURL() {

        if (stripos($this->payment_url, 'http') !== 0) {
            return href_to($this->payment_url);
        }

        return string_replace_keys_values($this->payment_url, $this->options);
    }

    /**
     * Возвращает массив дополнительных полей
     * для формы редиректа на оплату (страница /billing/order)
     * Или URL для редиректа
     * Если возвращает NULL, то возникла ошибка и её надо получить через getLastError()
     *
     * @param array $order Массив данных заказа
     * @return ?array|string
     */
    public function getPaymentFormFields($order) {
        return [];
    }

    /**
     * Подготавливает данные для оплаты перед редиректом на страницу оплаты
     *
     * @param cmsRequest $request
     * @param modelBilling $model
     * @return ?string
     */
    public function preparePayment(cmsRequest $request, modelBilling $model) {
        return null;
    }

    /**
     * Выполняет обработку от запроса платёжной системы об успешной оплате
     *
     * @param cmsRequest $request
     * @param modelBilling $model
     * @return ?string|array
     */
    public function processPayment(cmsRequest $request, modelBilling $model) {
        return null;
    }

    /**
     * Возвращает сумму к оплате, учитывая курс платёжной системы
     *
     * @param float|int|string $summ
     * @return string
     */
    public function getPaymentOrderSumm($summ) {
        return nf(round($summ * $this->rate, 2), 2, '', false);
    }

    /**
     * Возвращает ID заказа для информационной страницы успеха оплаты
     *
     * @param cmsRequest $request
     * @return type
     */
    public function getSuccessOrderId(cmsRequest $request) {
        return $request->get('tid', 0);
    }

    /**
     * Возвращает результат обработки запроса от платёжной системы
     *
     * @param string $text Любой текст
     * @param array $headers Массив HTTP хедеров
     * @return array
     */
    public function processPaymentResult(string $text, $headers = []) {
        return [
            'headers' => $headers,
            'body'    => $text
        ];
    }

    /**
     * Выполняет HTTP POST запрос
     *
     * @param string         $path URL
     * @param string|array   $data Массив параметров или JSON
     * @param array $headers Заголовки запроса
     * @return \stdClass
     */
    protected function callHttp(string $path, $data, array $headers = []) {

        $ch = curl_init($path);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

        $response = new stdClass();

        $response->body = curl_exec($ch);
        $response->http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $response->error = false;

        if ($response->body === false) {
            $response->error = curl_error($ch);
        } else {
            $response->body = json_decode($response->body, true) ?? $response->body;
        }

        curl_close($ch);

        return $response;
    }

    /**
     * Фиксирует ошибку и возвращает NULL
     *
     * @param string $text
     * @return null
     */
    public function error(string $text) {

        $this->last_error = $text;

        return null;
    }

    /**
     * Возвращает последнюю ошибку
     *
     * @return ?string
     */
    public function getLastError() {
        return $this->last_error;
    }

    /**
     * Создаёт лог файл текущей системы оплаты
     * И заносит в него переданный текст с датой
     *
     * @param string|array $text Текст или массив (массив будет преобразован в json)
     */
    protected function log($text) {

        $dir = cmsConfig::get('cache_path') . 'billing/';

        if (!is_dir($dir)) {
            @mkdir($dir, 0777, true);
        }

        $file = $dir . $this->name . '_pay_api.log';

        $fp = fopen($file, 'a+');

        $line = is_array($text) ? json_encode($text, JSON_UNESCAPED_UNICODE) : $text;

        fwrite($fp, '['.date('Y-m-d H:i:s').'] '.$line."\n");

        fclose($fp);

        return false;
    }

}
