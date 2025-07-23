<?php
/**
 * Класс HTTP ответа
 */
class cmsResponse {

    protected static $instance;

    /**
     * Константы для приведения названий HTTP-заголовков в единый вид
     */
    const UPPER = '_ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    const LOWER = '-abcdefghijklmnopqrstuvwxyz';

    /**
     * HTTP заголовки
     * @var array
     */
    protected $headers = [];

    /**
     * Тело HTTP ответа
     * @var array|string
     */
    protected $content = '';

    /**
     * Статус-код HTTP ответа
     * @var int
     */
    protected $status_code = 200;

    /**
     * Токен запроса
     * @var ?string
     */
    protected static $nonce = null;

    /**
     *
     * @param array|string $content Тело HTTP ответа
     * @param int $status Статус-код HTTP ответа
     */
    public function __construct($content = '', int $status = 200) {

        $this->setContent($content);
        $this->setStatusCode($status);
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self;
        }
        return self::$instance;
    }

    /**
     * Возвращает nonce запроса
     *
     * @return string
     */
    public static function getNonce() {

        if (self::$nonce === null) {
            self::$nonce = bin2hex(random_bytes(24));
        }

        return self::$nonce;
    }

    /**
     * Отправляет HTTP заголовки и содержимое
     *
     * @param bool $exit Завершать работу, true по умолчанию
     * @return void
     */
    public function send($exit = true) {

        $this->sendHeaders();
        $this->sendContent();

        if (!$exit) {
            return;
        }

        if (function_exists('fastcgi_finish_request')) {
            fastcgi_finish_request();
        } elseif (function_exists('litespeed_finish_request')) {
            litespeed_finish_request();
        } elseif (!in_array(PHP_SAPI, ['cli', 'phpdbg', 'embed'], true)) {
            flush();
        }

        exit;
    }

    /**
     * Отправляет HTTP заголовки, содержимое и завершает работу
     */
    public function sendAndExit() {
        $this->send();
    }

    /**
     * Отправляет содержимое для текущего HTTP ответа
     *
     * @return $this
     */
    public function sendContent() {

        if (is_array($this->content)) {

            $this->content = json_encode($this->content, JSON_UNESCAPED_UNICODE);

            if ($this->content === false) {
                $this->content = json_encode([
                    'success' => false,
                    'errors'  => true,
                    'error'   => json_last_error_msg()
                ]);
            }
        }

        echo $this->content;

        return $this;
    }

    /**
     * Отправляет HTTP заголовки
     *
     * @return $this
     */
    public function sendHeaders() {

        if (headers_sent()) {
            return $this;
        }

        if (!cmsConfig::get('disable_copyright')) {
            $this->setHeader('X-Powered-By', 'InstantCMS');
        }

        // RFC2616 - 14.18 все ответы должны иметь дату
        if (!$this->hasHeader('date')) {
            $this->initDateHeader();
        }

        foreach ($this->headers as $name => $values) {

            $replace = strcasecmp($name, 'Content-Type') === 0;

            foreach ($values as $value) {
                header($name . ': ' . $value, $replace, $this->status_code);
            }

        }

        http_response_code($this->status_code);

        return $this;
    }

    /**
     * Создает HTTP заголовки для файла и отправляет его содержимое
     *
     * @param string $file_path Полный путь к файлу
     * @param array $headers Массив заголовков
     * @return type
     */
    public function sendFile(string $file_path, array $headers = []) {

        if (!is_file($file_path)) {
            $file_path = cmsConfig::get('upload_path') . $file_path;
        }

        if (!is_readable($file_path)) {
            return $this->setContent(404)->setStatusCode(404)->sendAndExit();
        }

        $modified = filemtime($file_path);
        $body     = file_get_contents($file_path);
        $size     = strlen($body);

        $this->addHeaders($headers);

        if (!$this->hasHeader('Content-Type')) {

            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $file_mime = finfo_buffer($finfo, $body) ?: 'text/plain';
            finfo_close($finfo);

            $this->setHeader('Content-Type', $file_mime);
        }


        $this->setHeader('Last-Modified', gmdate('D, d M Y H:i:s', $modified) . ' GMT')->
               setHeader('Content-Length', $size);

        return $this->setContent($body)->sendAndExit();
    }

    /**
     * Создает HTTP заголовки и запускает скачивание файла
     *
     * @param string $file_path Полный путь к файлу
     * @param ?string $file_name Имя файла
     * @return type
     */
    public function sendDownloadFile(string $file_path, $file_name = null) {

        if (!$file_name) {
            $file_name = pathinfo($file_path, PATHINFO_BASENAME);
        }

        // Удаляем ранее установленный Content-Type чтобы автоматически по типу файла стал
        $this->removeHeader('Content-Type');

        return $this->sendFile($file_path, ['Content-Disposition' => 'attachment; filename="' . htmlspecialchars($file_name, ENT_COMPAT) . '"']);
    }

    /**
     * Устанавливает HTTP ответ, выполняющий редирект
     *
     * @param string $url URL для редиректа
     * @param int $status HTTP код
     */
    public function redirect($url, $status = 302) {

        // CWE-113
        $url = str_replace("\r\n", '', $url);

        $this->setStatusCode($status);

        $this->setHeader('Location', $url);

        return $this;
    }

    /**
     * Определяет, соответствует ли валидатор ответа Last-Modified
     * условному значению, указанному в запросе
     * Если ответ не модифицирован, устанавливает код состояния 304 и
     * удаляет фактическое содержимое, вызывая метод setNotModified()
     *
     * @param cmsRequest $request Объект запроса
     * @return bool
     */
    public function isNotModified(cmsRequest $request) {

        if (!$request->isMethodCacheable()) {
            return false;
        }

        $not_modified   = false;
        $last_modified  = $this->getHeader('Last-Modified');
        $modified_since = $request->getHeader('IF_MODIFIED_SINCE');

        if ($modified_since && $last_modified) {
            $not_modified = strtotime($modified_since) >= strtotime($last_modified);
        }

        if ($not_modified) {
            $this->setNotModified();
        }

        return $not_modified;
    }

    /**
     * Изменяет ответ таким образом, чтобы он соответствовал правилам, определенным для кода состояния 304
     * При этом устанавливается статус, удаляется тело и отбрасываются заголовки.
     * которые не должны быть включены в ответы 304
     *
     * @return $this
     *
     * @see https://tools.ietf.org/html/rfc2616#section-10.3.5
     */
    public function setNotModified() {

        $this->setStatusCode(304);
        $this->setContent('');

        foreach (['Allow', 'Content-Encoding', 'Content-Language', 'Content-Length', 'Content-MD5', 'Content-Type', 'Last-Modified'] as $name) {
            $this->removeHeader($name);
        }

        return $this;
    }

    /**
     * Устанавливает HTTP заголовок Last-Modified
     * При передаче значения null заголовок будет удален
     *
     * @param ?string $date Дата в строковом виде
     * @return $this
     */
    public function setLastModified($date) {

        if ($date === null) {

            $this->removeHeader('Last-Modified');

            return $this;
        }

        return $this->setHeader('Last-Modified', gmdate('D, d M Y H:i:s', strtotime($date)) . ' GMT');
    }

    /**
     * Устанавливает код состояния HTTP ответа
     *
     * @param int $code HTTP код
     * @return $this
     */
    public function setStatusCode(int $code) {

        $this->status_code = $code;

        return $this;
    }

    /**
     * Возвращает код состояния HTTP ответа
     *
     * @return int
     */
    public function getStatusCode() {
        return $this->status_code;
    }

    /**
     * Устанавливает содержимое HTTP ответа
     *
     * @param string|array $content Тело ответа. Если передан массив, будет выведет JSON
     * @return $this
     */
    public function setContent($content) {

        $this->content = $content;

        if (is_array($this->content)) {

            $this->setHeader('Content-type', 'application/json');
        }

        return $this;
    }

    /**
     * Устанавливает HTTP заголовок по имени
     *
     * @param string               $key Имя заголовка
     * @param string|string[]|null $values  Значение или массив значений
     * @param bool                 $replace Заменять значение или нет (по умолчанию true)
     * @return $this
     */
    public function setHeader(string $key, $values, bool $replace = true) {

        $key = $this->normalizeHeaderName($key);

        if (is_array($values)) {

            $values = array_values($values);

            if ($replace || !isset($this->headers[$key])) {
                $this->headers[$key] = $values;
            } else {
                $this->headers[$key] = array_merge($this->headers[$key], $values);
            }

        } else {

            if ($replace || !isset($this->headers[$key])) {
                $this->headers[$key] = [$values];
            } else {
                $this->headers[$key][] = $values;
            }
        }

        return $this;
    }

    /**
     * Возвращает HTTP заголовки
     *
     * @param ?string $key Имя заголовков, которые нужно вернуть, или null, чтобы получить их все
     * @return array
     */
    public function getHeaders($key = null) {

        if ($key !== null) {
            return $this->headers[$this->normalizeHeaderName($key)] ?? [];
        }

        return $this->headers;
    }

    /**
     * Возвращает первое значение HTTP заголовока по имени
     *
     * @param string $key Имя заголовка
     * @param ?string $default Значение по умолчанию, если заголовка нет
     * @return ?string
     */
    public function getHeader(string $key, $default = null) {

        $headers = $this->getHeaders($key);

        if (!$headers) {
            return $default;
        }

        if (!isset($headers[0])) {
            return null;
        }

        return (string) $headers[0];
    }

    /**
     * Заменяет текущие HTTP заголовки новым набором
     *
     * @param array $headers Новый массив заголовков
     */
    public function replaceHeaders(array $headers = []) {

        $this->headers = [];

        $this->addHeaders($headers);
    }

    /**
     * Добавляет новые заголовки в текущий набор HTTP заголовков
     *
     * @param array $headers Массив заголовков
     */
    public function addHeaders(array $headers) {
        foreach ($headers as $key => $values) {
            $this->setHeader($key, $values);
        }
    }

    /**
     * Возвращает true, если HTTP заголовок определен
     *
     * @param string $key Имя заголовка
     * @return bool
     */
    public function hasHeader(string $key) {
        return array_key_exists($this->normalizeHeaderName($key), $this->getHeaders());
    }

    /**
     * Возвращает true, если данный HTTP заголовок содержит заданное значение
     *
     * @param string $key Имя заголовка
     * @param string $value Значение
     * @return bool
     */
    public function containsHeader(string $key, string $value) {
        return in_array($value, $this->getHeaders($key), true);
    }

    /**
     * Удаляет HTTP заголовок
     *
     * @param string $key Имя заголовка
     */
    public function removeHeader(string $key) {
        unset($this->headers[$this->normalizeHeaderName($key)]);
    }

    /**
     * Добавляет заголовок Date
     */
    private function initDateHeader() {
        $this->setHeader('Date', gmdate('D, d M Y H:i:s') . ' GMT');
    }

    /**
     * Приводит название HTTP заголовка в стандартный вид
     *
     * @param string $key Имя заголовка
     * @return string
     */
    protected function normalizeHeaderName(string $key) {
         return strtr($key, self::UPPER, self::LOWER);
    }

}
