<?php
/**
 * Класс для работы с конфигурациями
 * из директории константы ICMS_CONFIG_DIR
 */
class cmsConfigs {

    /**
     * Массив конфигурации
     *
     * @var array
     */
    protected $data = [];

    /**
     * Динамические значения конфигурации,
     * которые не указаны в файле
     *
     * @var array
     */
    protected $dynamic = [];

    /**
     * Значения конфигурации,
     * которые были изменены
     *
     * @var array
     */
    protected $changed = [];

    /**
     * Значения конфигурации, как они есть в файле
     *
     * @var array
     */
    protected $config = [];

    /**
     * Объявить оригинальный массив конфигурации
     *
     * @var bool
     */
    protected $keep_original = false;

    /**
     * Файл конфигурации
     *
     * @var string
     */
    protected $file = '';

    /**
     * Флаг, что в конфигурации нужно искать локализованные данные
     *
     * @var boolean
     */
    protected $is_find_localized = false;

    /**
     * Загружает конфигурацию
     *
     * @param string $cfg_file Файл конфигурации из директории константы ICMS_CONFIG_DIR
     */
    public function __construct($cfg_file) {

        $this->file = $cfg_file;

        $this->load();
    }

    /**
     * Возвращает относительный путь к файлу
     *
     * @return string
     */
    public function getFilePath() {
        return ICMS_CONFIG_DIR . $this->file;
    }

    /**
     * Не искать локализованные данные
     */
    public function findLocalizedOff() {
        $this->is_find_localized = false;
    }

    /**
     * Искать локализованные данные
     */
    public function findLocalizedOn() {
        $this->is_find_localized = true;
    }

    /**
     * Возвращает true, если ищем локализованные данные
     *
     * @return boolean
     */
    public function isfindLocalized() {
        return $this->is_find_localized;
    }

    /**
     * Загружает массив конфигурации
     *
     * @return boolean
     */
    protected function load() {

        $cfg_file = PATH . $this->getFilePath();

        if (!is_readable($cfg_file)) {
            return false;
        }

        $this->setData(include $cfg_file);

        return true;
    }

    /**
     * Устанавливает/изменяет значение опции конфигурации
     *
     * @param string $key Ключ
     * @param mixed $value Значение
     * @return $this
     */
    public function set($key, $value) {

        // Нет такой опции в файле конфигурации
        if(!array_key_exists($key, $this->data)){
            $this->dynamic[] = $key;
        } else {
        // Если есть, фиксируем, что меняли
            $this->changed[$key] = $this->data[$key];
        }

        $this->data[$key] = $value;

        return $this;
    }

    public function __get($name) {

        if ($this->is_find_localized) {

            $value = get_localized_value($name, $this->data);

            return $value === null ? false : $value;
        }

        if (!array_key_exists($name, $this->data)) {
            return false;
        }

        return $this->data[$name];
    }

    public function __set($name, $value) {
        $this->set($name, $value);
    }

    public function __isset($name) {

        if ($this->is_find_localized) {
            return get_localized_value($name, $this->data) !== null;
        }

        return array_key_exists($name, $this->data);
    }

    public function __unset($name) {
        unset($this->data[$name]);
    }

    /**
     * Изменялся ли ключ конфигурации
     *
     * @param string $key
     * @return boolean
     */
    public function isChangedKey($key) {
        return array_key_exists($key, $this->changed);
    }

    /**
     * Динамический ли ключ (которого не было в файле конфигурации)
     *
     * @param string $key
     * @return boolean
     */
    public function isDynamicKey($key) {
        return in_array($key, $this->dynamic, true);
    }

    /**
     * Возвращает весь актуальный конфиг
     *
     * @return array
     */
    public function getAll() {
        return $this->data;
    }

    /**
     * Устанавливает конфиг
     *
     * @param array $data
     * @return $this
     */
    public function setData(array $data) {

        $this->changed = [];
        $this->dynamic = [];

        $this->data = $data;

        // Запоминаем оригинальный конфиг
        if ($this->keep_original) {
            $this->config = $this->data;
        }

        return $this;
    }

    /**
     * Возвращает весь конфиг, как он задан в файле
     * Если передан ключ, возвращает его значение
     *
     * @param ?string $key
     * @return mixed
     */
    public function getConfig($key = null) {
        if($key === null){
            return $this->config;
        }
        return array_key_exists($key, $this->config) ? $this->config[$key] : false;
    }

    /**
     * Сохраняет массив конфигурации в файл
     *
     * @param array $values
     * @return boolean
     */
    public function save($values) {

        $dump = '<?php' . PHP_EOL . PHP_EOL .
                'return [' . PHP_EOL;

        foreach ($values as $key => $value) {

            if ($this->isDynamicKey($key)) {
                continue;
            }

            $value = var_export($value, true);

            $gap = 28 - strlen($key);

            $dump .= str_repeat(' ', 4) . var_export($key, true);
            $dump .= str_repeat(' ', $gap > 0 ? $gap : 0);
            $dump .= '=> ' . $value . ',' . PHP_EOL;
        }

        $dump = rtrim($dump, ',' . PHP_EOL);

        $dump .= PHP_EOL . '];' . PHP_EOL;

        return $this->saveFileData($dump);
    }

    /**
     * Непосредственно сохраняет данные в файл
     *
     * @param string $dump
     * @return bool
     */
    protected function saveFileData($dump) {

        $file = PATH . $this->getFilePath();

        $success = false;

        if (is_writable($file)) {

            if (function_exists('opcache_reset')) {
                @opcache_reset();
            }

            $success = file_put_contents($file, $dump);
        }

        return $success;
    }

    /**
     * Сохраняет в файл одно значение по ключу
     *
     * @param string $key
     * @param mixed $value
     * @return boolean
     */
    public function update($key, $value) {

        $this->config[$key] = $value;

        $this->data[$key] = $value;

        return $this->save($this->config);
    }

}
