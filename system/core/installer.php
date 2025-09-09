<?php
/**
 * Установщик дополнений InstantCMS
 */
class cmsInstaller {

    /**
     * Полный путь к директории с распакованным пакетом
     *
     * @var string
     */
    private $package_path = '';

    /**
     * Объект контроллера админки
     *
     * @var admin
     */
    private $admin;

    /**
     * Для отслеживания что мы установили, если пакет не типизирован
     *
     * @var array
     */
    private $count_installed_before = [
        'widgets'     => 0,
        'controllers' => 0
    ];

    /**
     * Ошибка при установке пакета
     *
     * @var mixed
     */
    private $install_error = false;

    /**
     * Данные манифеста дополнения
     *
     * @var array
     */
    private $manifest = [];

    /**
     * Пространство имён файла установщика
     *
     * @var string
     */
    private $installer_namespace = '';

    /**
     * Нам нужно проставлять пространство имён
     * В файле install.php пакета
     *
     * @var bool
     */
    private $use_namespace = false;

    /**
     * Файлы, которые установщик пакета не удалил
     * Их необходимо удалить вручную
     *
     * @var array
     */
    private $undeleted_files = [];

    /**
     * Работает с пакетом дополнения, распакованным по пути $package_path
     *
     * @param string $package_path Полный путь к директории с распакованным пакетом, без конечного слэша
     * @param admin $admin_controller Объект контроллера админки
     */
    public function __construct(string $package_path, admin $admin_controller) {

        $this->admin = $admin_controller;

        $this->package_path = $package_path;

        $this->manifest = $this->parsePackageManifest();

        $this->loadManifestDepends();
    }

    /**
     * Включаем пространство имён
     * В файле install.php пакета
     * Будет добавлено автоматически
     * Нужно использовать, если устанавливаются несколько дополнений за раз
     */
    public function useNamespace() {

        $this->use_namespace = true;

        return $this;
    }

    /**
     * Возвращает ошибку установки
     *
     * @return string
     */
    public function getInstallError() {
        return $this->install_error;
    }

    /**
     * Очищает директорию с распакованным пакетом
     *
     * @return bool
     */
    public function clear() {
        return files_clear_directory($this->package_path);
    }

    /**
     * Возвращает директорию с файлами пакета
     *
     * @return string
     */
    public function getPackageContentsDir() {
        return $this->package_path . '/package';
    }

    /**
     * Возвращает массив манифеста пакета
     *
     * @return array
     */
    public function getManifest() {
        return $this->manifest;
    }

    /**
     * Устанавливает id дополнения для манифеста
     *
     * @param int $addon_id id дополнения из каталога InstantCMS
     */
    public function setManifestAddonId($addon_id) {
        // Может быть указано в манифесте
        if (empty($this->manifest['info']['addon_id'])) {
            $this->manifest['info']['addon_id'] = $addon_id ?: null;
        }
    }

    /**
     * Выполняет установку
     * Импортирует SQL и вызывает функцию установки из пакета
     *
     * @return null|string
     */
    public function install() {

        $this->loadInstalledCounts();

        clearstatcache();

        $is_imported = $this->importPackageDump();

        if ($is_imported) {
            $is_installed = $this->runPackageInstaller();
        } else {
            $is_installed = false;
        }

        if (!$is_installed) {
            return null;
        }

        $this->deleteUnusedFiles();

        $redirect_action = $this->doPackage();

        // если в файле install.php есть функция after_install_package, вызываем ее
        // этот файл, если он есть, уже должен был загружен ранее
        $this->callInstallFunc('after_install_package');

        return $redirect_action;
    }

    /**
     * Удаляет ненужные файлы после установки/обновления
     * Файл в пакете должен быть назван deleted.files.txt
     * В нём, относительно корня установки, без начального слэша,
     * необходимо указать пути к файлам, каждый с новой строки
     *
     * @return bool
     */
    private function deleteUnusedFiles() {

        $deleted_files_path = $this->package_path . '/deleted.files.txt';

        if (!is_readable($deleted_files_path)) {
            return false;
        }

        $root = cmsConfig::get('root_path');

        $files = file($deleted_files_path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) ?: [];

        foreach ($files as $path) {
            if (strpos($path, '#') === 0) {
                continue;
            }
            if (file_exists($root . $path)) {
                if (!@unlink($root . $path)) {
                    $this->undeleted_files[] = $path;
                }
            }
        }

        return true;
    }

    /**
     * Возвращает файлы, которые установщик не смог удалить
     *
     * @return array
     */
    public function getUndeletedFiles() {
        return $this->undeleted_files;
    }

    /**
     * Выполняет пост-установочные действия
     *
     * @return string
     */
    private function doPackage() {

        $install_method = 'otherInstall';

        if ($this->manifest['package']) {

            $_install_method = $this->manifest['package']['type'] . ucfirst($this->manifest['package']['action']);

            if (method_exists($this, $_install_method)) {

                $install_method = $_install_method;
            }
        }

        $success = call_user_func([$this, $install_method]);

        if (!empty($this->manifest['package_controllers'])) {

            foreach ($this->manifest['package_controllers'] as $package_controller) {
                $this->updateEvents($package_controller);
            }

        } else {
            $this->updateEvents();
        }

        // Очищаем нужный кэш
        $cache = cmsCache::getInstance();

        $cache->clean('controllers');
        $cache->clean('events');
        $cache->clean('widgets.bind');
        $cache->clean('widgets.bind_pages');

        // Очищаем css и js кэш
        foreach (['css', 'js'] as $type) {

            $cache_folder_path = cmsConfig::get('root_path') . "cache/static/{$type}";

            files_clear_directory($cache_folder_path);
        }

        // Если задан абстрактный счётчик, увеличиваем на единицу
        // Если он не задан, то вероятно администратор сайта это сделал
        // осознано для самостоятельной отладки
        if (cmsConfig::get('production_time') > 0) {

            $values = cmsConfig::getInstance()->getConfig();
            $values['production_time'] += 1;

            cmsConfig::getInstance()->save($values);
        }

        return $success;
    }

    /**
     * Возвращает общие данные для всех дополнений
     *
     * @return array
     */
    private function preparePackageData() {

        return [
            'title'       => $this->manifest['info']['title'],
            'name'        => $this->manifest['package']['name'] ?? null,
            'author'      => $this->manifest['author']['name'] ?? LANG_CP_PACKAGE_NONAME,
            'url'         => $this->manifest['author']['url'] ?? null,
            'version'     => $this->manifest['version_str'],
            'files'       => $this->manifest['contents'] ?: null,
            'addon_id'    => $this->manifest['info']['addon_id'],
            'is_external' => 1
        ];
    }

    /**
     * Копирует файл подсказки виджета и возвращает его имя
     *
     * @return null|string
     */
    private function copyWidgetImageHint() {

        $image_hint = $this->manifest['info']['image_hint'] ?? null;

        if (!$image_hint || !is_file($image_hint)) {
            return null;
        }

        $controller = $this->manifest['package']['controller'] ?? '';

        $extension = strtolower(pathinfo($image_hint, PATHINFO_EXTENSION));
        $file_path = sprintf(
                'package-images/widgets/%s%s.%s',
                $controller ? $controller . '_' : '',
                $this->manifest['package']['name'],
                $extension
        );

        $destination = cmsConfig::get('upload_path') . $file_path;

        return @copy($image_hint, $destination) ? $file_path : null;
    }

    /**
     * Регистрирует виджет в БД и возвращает имя экшена для редиректа
     *
     * @return string
     */
    private function widgetInstall() {

        $data = $this->preparePackageData();

        $data['controller'] = $this->manifest['package']['controller'];
        $data['image_hint_path'] = $this->copyWidgetImageHint();

        $this->admin->model->insert('widgets', $data);

        return 'widgets';
    }

    /**
     * Обновляет данные виджета в БД и возвращает имя экшена для редиректа
     *
     * @return string
     */
    private function widgetUpdate() {

        $update_data = $this->preparePackageData();

        $update_data['controller'] = $this->manifest['package']['controller'];
        $update_data['image_hint_path'] = $this->copyWidgetImageHint();

        $installed_widget = $this->admin->model->filterEqual('name', $update_data['name'])->
                filterEqual('controller', $update_data['controller'])->
                getItem('widgets', function ($item) {
            $item['files'] = cmsModel::yamlToArray($item['files']);
            return $item;
        });

        if (!empty($update_data['files'])) {
            if (!empty($installed_widget['files'])) {
                $update_data['files'] = multi_array_unique(array_merge_recursive($installed_widget['files'], $update_data['files']));
            }
        }

        $this->admin->model->filterEqual('name', $update_data['name'])->
                filterEqual('controller', $update_data['controller'])->
                updateFiltered('widgets', $update_data);

        return 'widgets';
    }

    /**
     * Подготавливает данные для пакетов контроллеров
     *
     * @return array
     */
    private function prepareControllerData() {

        $controller_root_path = cmsConfig::get('root_path') .
                'system/controllers/' . $this->manifest['package']['name'] . '/';

        $form_file = $controller_root_path . 'backend/forms/form_options.php';
        $form_name = $this->manifest['package']['name'] . 'options';

        cmsCore::loadControllerLanguage($this->manifest['package']['name']);

        $form = cmsForm::getForm($form_file, $form_name, [[]]);

        if ($form && is_readable($controller_root_path . 'backend.php')) {

            $backend_controller = $this->admin->loadControllerBackend($this->manifest['package']['name'], new cmsRequest([], cmsRequest::CTX_INTERNAL));

            $form = $backend_controller->addControllerSeoOptions($form);

            $options = $form->parse(new cmsRequest(cmsController::loadOptions($this->manifest['package']['name'])));

        } else {

            $options = cmsController::loadOptions($this->manifest['package']['name']);
        }

        $data = $this->preparePackageData();

        $data['options'] = $options ?: null;
        $data['is_backend'] = file_exists($controller_root_path . 'backend.php');

        return $data;
    }

    /**
     * Регистрирует компонент в БД и возвращает имя экшена для редиректа
     *
     * @return string
     */
    private function componentInstall() {

        $data = $this->prepareControllerData();

        $this->admin->model->insert('controllers', $data);

        return $data['is_backend'] ? 'controllers/edit/'.$data['name'] : 'controllers';
    }

    /**
     * Обновляет данные компонента в БД и возвращает имя экшена для редиректа
     *
     * @return string
     */
    private function componentUpdate() {

        $update_data = $this->prepareControllerData();

        $installed_controller = $this->admin->model->getControllerInfo($update_data['name']);

        if (!empty($update_data['files'])) {
            if (!empty($installed_controller['files'])) {
                $update_data['files'] = multi_array_unique(array_merge_recursive($installed_controller['files'], $update_data['files']));
            }
        }

        $this->admin->model->filterEqual('name', $update_data['name'])->
                updateFiltered('controllers', $update_data);

        return $update_data['is_backend'] ? 'controllers/edit/'.$update_data['name'] : 'controllers';
    }

    /**
     * Ищет установленное за текущую сессию
     * Нетипизированное дополнение и
     * Дополняет данными
     * Предполагается, что для таких дополнений в install_package()
     * Своя логика регистраций в БД
     *
     * @return string
     */
    private function otherInstall() {

        $redirect_action = '';

        $count_installed_before = $this->count_installed_before;

        $this->loadInstalledCounts();

        $update_data = [
            'addon_id'    => $this->manifest['info']['addon_id'],
            'files'       => $this->manifest['contents'] ?: null,
            'is_external' => 1
        ];

        // если установили виджет
        if ($this->count_installed_before['widgets'] > $count_installed_before['widgets']) {

            $this->admin->model->orderBy('id', 'desc')->
                    limit($this->count_installed_before['widgets'] - $count_installed_before['widgets']);

            $widgets_ids = $this->admin->model->selectOnly('id')->
                    get('widgets', function ($item, $model) {
                return $item['id'];
            }, false);

            $this->admin->model->filterIn('id', $widgets_ids)->updateFiltered('widgets', $update_data, true);

            $redirect_action = 'widgets';
        }

        // если установили компонент
        if ($this->count_installed_before['controllers'] > $count_installed_before['controllers']) {

            $this->admin->model->orderBy('id', 'desc')->
                    limit($this->count_installed_before['controllers'] - $count_installed_before['controllers']);

            $controllers_ids = $this->admin->model->selectOnly('id')->
                    get('controllers', function ($item, $model) {
                return $item['id'];
            }, false);

            $this->admin->model->filterIn('id', $controllers_ids)->updateFiltered('controllers', $update_data, true);

            $redirect_action = 'controllers';
        }

        return $redirect_action;
    }

    private function systemInstall() {
        return '';
    }

    private function systemUpdate() {
        return '';
    }

    /**
     * Вызывает функцию install_package дополнения, если есть файл install.php
     *
     * @return bool
     */
    private function runPackageInstaller() {

        $file = $this->package_path . '/' . 'install.php';

        // нет файла, считаем, что так задумано и ошибку не отдаем
        if (!file_exists($file)) {
            return true;
        }

        @chmod($file, 0666);

        if (!is_readable($file)) {

            $this->install_error = sprintf(LANG_CP_INSTALL_PERM_ERROR, $file);

            return false;
        }

        if ($this->use_namespace) {
            $this->setNamespace($file);
        }

        include_once $file;

        return $this->callInstallFunc('install_package');
    }

    /**
     * Устанавливает пространство имён в файл install.php пакета
     *
     * @param type $file
     */
    private function setNamespace(string $file) {

        $install_php_text = file_get_contents($file);

        $this->installer_namespace = 'installer\\' . str_replace('/', '\\', str_replace(cmsConfig::get('root_path'), '', $this->package_path));

        $namespace_str = 'namespace '.$this->installer_namespace.';';

        if (mb_strpos($install_php_text, $namespace_str) === false) {

            $pos = mb_strpos($install_php_text, '<?php');

            $modified = mb_substr($install_php_text, $pos, 5).PHP_EOL.$namespace_str.mb_substr($install_php_text, 5);

            file_put_contents($file, $modified);
        }

        // Делаем автозагрузку и алиас
        spl_autoload_register(function (string $class_name) {

            if (strpos($class_name, 'installer\\') === 0) {

                $native_name = substr(strrchr($class_name, '\\'), 1);

                class_alias($native_name, $class_name);
            }
        }, true, true);
    }

    /**
     * Запускает функцию внутри install.php пакета
     *
     * @param string $name Имя функции
     * @return bool
     */
    private function callInstallFunc(string $name) {

        $func = $this->installer_namespace.'\\'.$name;

        if (!function_exists($func)) {
            return false;
        }

        $result = call_user_func($func);

        if (is_string($result)) {

            $this->install_error = $result;

            return false;
        }

        return $result;
    }

    /**
     * Импортирует SQL дамп дополнения, если он есть
     *
     * @return bool
     */
    private function importPackageDump() {

        $file = $this->package_path . '/' . 'install.sql';

        if (!file_exists($file)) {
            return true;
        }

        @chmod($file, 0666);

        if (!is_readable($file)) {

            $this->install_error = sprintf(LANG_CP_INSTALL_PERM_ERROR, $file);

            return false;
        }

        $success = $this->admin->model->db->importDump($file);

        // Если файл пустой, будет NULL
        return $success === false ? false : true;
    }

    /**
     * Подключает и разбирает ini файл манифеста дополнения
     *
     * @return array
     */
    private function parsePackageManifest() {

        $ini_file         = $this->package_path . '/manifest.'.cmsConfig::get('language').'.ini';
        $ini_file_default = $this->package_path . '/manifest.ru.ini';

        if (!is_readable($ini_file)) {
            $ini_file = $ini_file_default;
        }
        if (!is_readable($ini_file)) {
            return [];
        }

        $manifest = parse_ini_file($ini_file, true);

        $manifest['contents'] = [];
        $manifest['package']  = [];
        $manifest['package_controllers'] = [];
        $manifest['info']['addon_id'] = $manifest['info']['addon_id'] ?? null;
        $manifest['version_str'] = $manifest['version']['major'] . '.' . $manifest['version']['minor'] . '.' . $manifest['version']['build'];

        if (is_dir($this->package_path . '/' . 'package')) {

            $manifest['contents'] = $this->getPackageContentsList();

            $this->checkSystemFiles($manifest);
        }

        if (isset($manifest['info']['image_hint'])) {
            $manifest['info']['image_hint'] = $this->package_path . '/' . $manifest['info']['image_hint'];
        }

        if (isset($manifest['install']) || isset($manifest['update'])) {

            $action = isset($manifest['install']) ? 'install' : 'update';

            if (isset($manifest[$action]['type']) && isset($manifest[$action]['name'])) {

                $manifest['package'] = [
                    'type'       => $manifest[$action]['type'],
                    'type_hint'  => string_lang('LANG_CP_PACKAGE_TYPE_' . $manifest[$action]['type'] . '_' . $action),
                    'action'     => $action,
                    'name'       => $manifest[$action]['name'],
                    'controller' => $manifest[$action]['controller'] ?? null,
                ];

                // проверяем установленную версию
                $installed_method = $manifest[$action]['type'] . 'Installed';
                if (method_exists($this, $installed_method)) {
                    $manifest['package']['installed_version'] = call_user_func([$this, $installed_method], $manifest['package']);
                }
            }
        }

        // проверяем наличие контроллеров и манифестов
        if (!empty($manifest['package_controllers']['controller'])) {

            $manifest['package_controllers'] = $manifest['package_controllers']['controller'];

        } else {

            $dir = $this->package_path . '/package/system/controllers';

            if (is_dir($dir)) {

                $manifest['package_controllers'] = files_get_dirs_list($dir, true);
            }
        }

        return $manifest;
    }

    /**
     * Проверяет, изменяет ли пакет системные файлы
     *
     * @param array $manifest
     * @return void
     */
    private function checkSystemFiles(array &$manifest) {

        $system_paths = [
            'core'   => 'system/core/',
            'config' => 'system/config/'
        ];

        foreach ($system_paths as $key => $path) {
            if (!empty($manifest['contents']['system'][$key])) {
                foreach ($manifest['contents']['system'][$key] as $file) {
                    if (file_exists(cmsConfig::get('root_path') . $path . $file)) {
                        $manifest['notice_system_files'] = LANG_INSTALL_NOTICE_SYSTEM_FILE;
                        return;
                    }
                }
            }
        }
    }

    /**
     * Проверяем зависимости и сохраняем в манифест
     */
    private function loadManifestDepends() {

        if (!$this->manifest) {
            return;
        }

        $results = [];

        // Проверка зависимости от версии ядра
        $results['core'] = isset($this->manifest['depends']['core']) &&
                version_compare(cmsCore::getVersion(), $this->manifest['depends']['core']) >= 0;

        // Проверка зависимости от версии другого пакета
        $results['package'] = isset($this->manifest['depends']['package'], $this->manifest['package']['installed_version']) &&
                version_compare((string) $this->manifest['package']['installed_version'], $this->manifest['depends']['package']) >= 0;

        // Проверка зависимости от зависимого типа
        if (isset($this->manifest['depends']['dependent_type'], $this->manifest['depends']['dependent_name'])) {

            $installed_version = call_user_func([$this, $this->manifest['depends']['dependent_type'] . 'Installed'], [
                'name'       => $this->manifest['depends']['dependent_name'],
                'controller' => $this->manifest['depends']['dependent_controller'] ?? null,
            ]);

            $results['dependent_type'] = $installed_version !== false;

            if ($results['dependent_type'] && isset($this->manifest['depends']['dependent_version'])) {
                $results['dependent_version'] = version_compare((string) $installed_version, $this->manifest['depends']['dependent_version']) >= 0;
            }
        }

        // Проверка зависимости от версии PHP
        $results['php'] = isset($this->manifest['depends']['php']) &&
                version_compare(PHP_VERSION, $this->manifest['depends']['php']) >= 0;

        // Проверка модулей php
        $results['php_ext'] = [];
        // Функция phpversion может быть отключена
        if (!empty($this->manifest['depends']['php_ext']) && function_exists('phpversion')) {
            foreach ($this->manifest['depends']['php_ext'] as $ext => $version) {

                $loaded_version = phpversion($ext);

                $results['php_ext'][$ext] = [
                    'loaded'           => $loaded_version,
                    'required_version' => $version,
                    'valid'            => $loaded_version && version_compare($loaded_version, $version) >= 0
                ];
            }
        }

        // Сохранение в манифест
        $this->manifest['depends_results'] = $results;
    }

    /**
     * Возвращает версию компонента, если установлен
     *
     * @param array $manifest_package
     * @return array
     */
    private function componentInstalled($manifest_package) {

        return $this->admin->model->filterEqual('name', $manifest_package['name'])->
                getFieldFiltered('controllers', 'version');
    }

    /**
     * Возвращает версию виджета, если установлен
     *
     * @param array $manifest_package
     * @return array
     */
    private function widgetInstalled(array $manifest_package) {

        return $this->admin->model->filterEqual('name', $manifest_package['name'])->
                    filterEqual('controller', $manifest_package['controller'])->
                    getFieldFiltered('widgets', 'version');
    }

    /**
     * Загружает кол-во виджетов и контроллеров из БД
     */
    private function loadInstalledCounts() {

        $this->admin->model->resetFilters();

        $this->count_installed_before = [
            'widgets'     => $this->admin->model->getCount('widgets', 'id', true),
            'controllers' => $this->admin->model->getCount('controllers', 'id', true)
        ];
    }

    /**
     * Возвращает дерево файлов
     *
     * @return bool
     */
    public function getPackageContentsList() {

        $path = $this->package_path . '/' . 'package';

        if (!is_dir($path)) {
            return [];
        }

        return files_tree_to_array($path);
    }

    /**
     * Обновляет события для контроллера
     *
     * @param string $controller_name Имя контроллера
     * @return bool
     */
    private function updateEvents(string $controller_name = '') {

        $diff_events = $this->admin->getEventsDifferences($controller_name);

        if ($diff_events['added']) {
            foreach ($diff_events['added'] as $controller => $events) {
                foreach ($events as $event) {
                    $this->admin->model->addEvent($controller, $event);
                }
            }
        }

        if ($diff_events['deleted']) {
            foreach ($diff_events['deleted'] as $controller => $events) {
                foreach ($events as $event) {
                    $this->admin->model->deleteEvent($controller, $event);
                }
            }
        }

        return true;
    }

}
