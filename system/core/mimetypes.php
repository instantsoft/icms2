<?php
/**
 * Управление типами MIME и расширениями файлов
 */
class cmsMimetypes extends cmsConfigs {

    protected $keep_original = true;

    private $extensions = [];

    public function __construct($cfg_file = 'mimetypes.php') {
        parent::__construct($cfg_file);
    }

    /**
     * Возвращает массив расширений по переданному типу MIME
     *
     * @param string $mime_type Тип MIME
     * @return ?array
     */
    public function getExtensions(string $mime_type) {
        return $this->extensions[$mime_type] ?? $this->extensions[strtolower($mime_type)] ?? null;
    }

    /**
     * Возвращает типы MIME по переданному расширению
     *
     * @param string $ext Расширение файла
     * @return ?array
     */
    public function getMimeTypes(string $ext) {

        $mime_types = $this->data[$ext] ?? $this->data[strtolower($ext)] ?? null;

        if ($mime_types === null) {
            return null;
        }

        return is_array($mime_types) ? $mime_types : [$mime_types];
    }

    /**
     * Загружает массив конфигурации
     *
     * @return boolean
     */
    protected function load() {

        parent::load();

        foreach ($this->data as $extension => $mime_types) {
            if (is_array($mime_types)) {
                foreach ($mime_types as $mime_type) {
                    $this->extensions[$mime_type][] = $extension;
                }
            } else {
                $this->extensions[$mime_types][] = $extension;
            }
        }

        return true;
    }

    /**
     * Сохраняет массив MIME типов в файл
     *
     * @param array $mimetypes
     * @return boolean
     */
    public function save($mimetypes) {

        $dump = '<?php' . PHP_EOL . PHP_EOL .
                'return [' . PHP_EOL;

        foreach ($mimetypes as $item) {

            $value_str = '';

            $mimes = explode("\n", $item['mimes']);

            if (count($mimes) > 1) {

                foreach ($mimes as $k => $mime) {
                    $mimes[$k] = var_export(strtolower(trim($mime)), true);
                }

                $value_str = '[' . implode(', ', $mimes) . ']';

            } else {

                $value_str = var_export(strtolower(trim($item['mimes'])), true);
            }

            $key = strtolower(trim($item['extension']));

            $gap = 10 - strlen($key);

            $dump .= str_repeat(' ', 4) . var_export($key, true);
            $dump .= str_repeat(' ', $gap > 0 ? $gap : 0);
            $dump .= '=> ' . $value_str . ',' . PHP_EOL;
        }

        $dump = rtrim($dump, ',' . PHP_EOL);

        $dump .= PHP_EOL . '];' . PHP_EOL;

        return $this->saveFileData($dump);
    }

}
