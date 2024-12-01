<?php
/**
 * Класс автозагрузки InstantCMS
 */
class cmsAutoloader {

    /**
     * @var ?cmsAutoloader
     */
    private static $instance;

    /**
     * Сопоставление неймспейсов и путей
     *
     * @var array
     */
    private $paths = [];

    private function __construct() {

        // Системные классы отдельно, так сложилось исторически
        spl_autoload_register([$this, 'loadCoreClass']);

        spl_autoload_register([$this, 'load']);
    }

    private function __clone() {}

    private static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self;
        }
        return self::$instance;
    }

    /**
     * Инициализация, чтобы отработал конструктор
     */
    public static function init() {
        self::getInstance();
    }

    /**
     * Регистрирует класс/набор классов по неймспейсу или имени
     *
     * @param string $namespace Базовый неймспейс
     * @param string $directory Путь до директории, относительно корня InstantCMS
     * @return void
     */
    public static function register(string $namespace, string $directory) {

        $self = self::getInstance();

        $self->paths[$namespace] = $directory;
    }

    /**
     * Регистрирует массив классов
     * Пример массива в system/config/autoload.php
     *
     * @param array $list
     * @return void
     */
    public static function registerList(array $list) {

        $self = self::getInstance();

        foreach ($list as $item) {
            $self->paths[$item[0]] = $item[1];
        }
    }

    /**
     * Подключает искомый файл с классом
     * Коллбэк spl_autoload_register
     *
     * @param string $class
     * @return bool
     */
    private function load(string $class) {

        $file = '';

        $namespace = '';

        $parts = explode('\\', $class);

        foreach ($parts as $part) {

            $namespace .= ($namespace ? '\\' : '') . $part;

            if (isset($this->paths[$namespace])) {

                $file = $this->paths[$namespace] . trim(str_replace('\\', '/', substr($class, strlen($namespace))), '/') . '.php';

                break;
            }
        }

        return $this->includeFile($file);
    }

    /**
     * Подключает системные классы:
     * system/core/
     * system/fields/
     * Модели контроллеров
     * Неймспейсы с префиксом icms\, относительно директории system/
     *            полностью повторяя вложенное дерево директорий
     *
     * Коллбэк spl_autoload_register
     *
     * @param string $_class_name
     * @return bool
     */
    private function loadCoreClass(string $_class_name) {

        $class_name = strtolower($_class_name);
        $class_file = false;
        $is_model   = false;

        if (strpos($class_name, 'cms') === 0) {

            $class_file = 'system/core/' . substr($class_name, 3) . '.php';

        } else
        if (strpos($class_name, 'field') === 0) {

            $class_file = 'system/fields/' . substr($class_name, 5) . '.php';

        } else
        if (strpos($class_name, 'model') === 0) {

            $cut_num = 5; $path = '';

            if (strpos($class_name, 'modelbackend') === 0) {
                $cut_num = 12; $path = '/backend';
            }

            $controller = strtolower(
                preg_replace(
                    ['/([A-Z]+)/', '/_([A-Z]+)([A-Z][a-z])/'],
                    ['_$1', '_$1_$2'],
                    lcfirst(substr($_class_name, $cut_num))
                )
            );

            $class_file = 'system/controllers/' . $controller . $path . '/model.php';

            $is_model = true;

        } else
        if (strpos($class_name, 'icms\\') === 0) {

            $class_file = 'system/' . str_replace('\\', '/', substr($_class_name, 5)) . '.php';
        }

        return $this->includeFile($class_file, $is_model);
    }

    /**
     * Подключает файл через include_once
     * по переданному относительному пути
     *
     * @param string $rel_path Относительный путь внутри директории InstantCMS
     * @param bool $throw Выбрасывать исключение, если файл не найден
     * @return bool
     * @throws Exception
     */
    private function includeFile($rel_path, $throw = false) {

        if (!$rel_path) {
            return false;
        }

        $path = PATH . '/' . $rel_path;

        if (!is_readable($path)) {

            if ($throw) {
                throw new Exception($rel_path);
            }

            return false;
        }

        include_once $path;

        return true;
    }

}

// Инициализируем сразу
cmsAutoloader::init();
