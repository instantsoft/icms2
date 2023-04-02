<?php

class actionAdminInstallFinish extends cmsAction {

    /**
     * Для отслеживания что мы установили, если пакет не типизирован
     * @var array
     */
    private $count_installed_before = [
        'widgets'     => 0,
        'controllers' => 0
    ];

    public function run() {

        $this->loadInstalledCounts();

        $path          = $this->cms_config->upload_path . $this->installer_upload_path;
        $path_relative = $this->cms_config->upload_root . $this->installer_upload_path;

        clearstatcache();

        $installer_path = $path . '/' . 'install.php';
        $sql_dump_path  = $path . '/' . 'install.sql';

        $is_imported = $this->importPackageDump($sql_dump_path);

        if ($is_imported) {
            $is_installed = $this->runPackageInstaller($installer_path);
        } else {
            $is_installed = false;
        }

        // считаем, что пришла ошибка
        if (is_string($is_installed)) {

            cmsUser::addSessionMessage($is_installed, 'error');

            $this->redirectToAction('install');
        }
        // или ошибка уже сформирована в функции установки через addSessionMessage
        if ($is_installed === false) {

            cmsUser::addSessionMessage(LANG_CP_INSTALL_ERROR, 'error');

            $this->redirectToAction('install');
        }

        $redirect_action = '';

        if ($is_imported && $is_installed === true) {

            $redirect_action = $this->doPackage();

            // если в файле install.php есть функция after_install_package, вызываем ее
            // этот файл, если он есть, уже должен был загружен ранее
            if (function_exists('after_install_package')) {
                call_user_func('after_install_package');
            }
        }

        $is_cleared = files_clear_directory($path);

        return $this->cms_template->render('install_finish', [
            'is_cleared'      => $is_cleared,
            'redirect_action' => $redirect_action,
            'path_relative'   => $path_relative
        ]);
    }

    private function doPackage() {

        $manifest = $this->parsePackageManifest();

        $success = '';

        if (isset($manifest['package'])) {

            $success = call_user_func([$this, $manifest['package']['type'] . $manifest['package']['action']], $manifest);
        } else {

            $this->otherInstall($manifest);
        }

        if (!empty($manifest['package_controllers'])) {

            foreach ($manifest['package_controllers'] as $package_controller) {
                $this->updateEvents($package_controller);
            }
        }

        // Очищаем нужный кэш
        $cache = cmsCache::getInstance();

        $cache->clean('controllers');
        $cache->clean('events');
        $cache->clean('widgets.bind');
        $cache->clean('widgets.bind_pages');

        // Очищаем css и js кэш
        foreach (['css', 'js'] as $type) {

            $cache_folder_path = $this->cms_config->root_path . "cache/static/{$type}";

            files_clear_directory($cache_folder_path);
        }

        // Если задан абстрактный счётчик, увеличиваем на единицу
        // Если он не задан, то вероятно администратор сайта это сделал
        // осознано для самостоятельной отладки
        if ($this->cms_config->production_time > 0) {

            $values = $this->cms_config->getConfig();
            $values['production_time'] += 1;

            $this->cms_config->save($values);
        }

        return $success;
    }

    private function componentInstall($manifest) {

        $model = new cmsModel();

        $controller_root_path = $this->cms_config->root_path . 'system/controllers/' . $manifest['package']['name'] . '/';

        $form_file = $controller_root_path . 'backend/forms/form_options.php';
        $form_name = $manifest['package']['name'] . 'options';

        cmsCore::loadControllerLanguage($manifest['package']['name']);

        $form = cmsForm::getForm($form_file, $form_name, [[]]);

        if ($form && is_readable($controller_root_path . 'backend.php')) {

            $backend_controller = $this->loadControllerBackend($manifest['package']['name'], new cmsRequest([], cmsRequest::CTX_INTERNAL));

            $form = $backend_controller->addControllerSeoOptions($form);

            $options = $form->parse(new cmsRequest([]));
        } else {
            $options = null;
        }

        $model->insert('controllers', [
            'title'       => $manifest['info']['title'],
            'name'        => $manifest['package']['name'],
            'options'     => $options,
            'author'      => (isset($manifest['author']['name']) ? $manifest['author']['name'] : LANG_CP_PACKAGE_NONAME),
            'url'         => (isset($manifest['author']['url']) ? $manifest['author']['url'] : null),
            'version'     => $manifest['version']['major'] . '.' . $manifest['version']['minor'] . '.' . $manifest['version']['build'],
            'is_backend'  => (file_exists($controller_root_path . 'backend.php') || $form),
            'files'       => (!empty($manifest['contents']) ? $manifest['contents'] : null),
            'addon_id'    => (!empty($manifest['info']['addon_id']) ? (int) $manifest['info']['addon_id'] : null),
            'is_external' => 1
        ]);

        return $options ? 'controllers/edit/'.$manifest['package']['name'] : 'controllers';
    }

    private function componentUpdate($manifest) {

        $model = new cmsModel();

        $controller_root_path = $this->cms_config->root_path . 'system/controllers/' . $manifest['package']['name'] . '/';

        $form_file = $controller_root_path . 'backend/forms/form_options.php';
        $form_name = $manifest['package']['name'] . 'options';

        cmsCore::loadControllerLanguage($manifest['package']['name']);

        $form = cmsForm::getForm($form_file, $form_name, [[]]);

        if ($form && is_readable($controller_root_path . 'backend.php')) {

            $backend_controller = $this->loadControllerBackend($manifest['package']['name'], new cmsRequest([], cmsRequest::CTX_INTERNAL));

            $form = $backend_controller->addControllerSeoOptions($form);

            $options = $form->parse(new cmsRequest(cmsController::loadOptions($manifest['package']['name'])));
        } else {
            $options = cmsController::loadOptions($manifest['package']['name']);
        }

        $update_data = [
            'title'      => $manifest['info']['title'],
            'options'    => $options ? $options : null,
            'author'     => (isset($manifest['author']['name']) ? $manifest['author']['name'] : LANG_CP_PACKAGE_NONAME),
            'url'        => (isset($manifest['author']['url']) ? $manifest['author']['url'] : null),
            'version'    => $manifest['version']['major'] . '.' . $manifest['version']['minor'] . '.' . $manifest['version']['build'],
            'is_backend' => file_exists($controller_root_path . 'backend.php')
        ];

        $installed_controller = $this->model->getControllerInfo($manifest['package']['name']);

        if (!empty($manifest['contents'])) {
            if (!empty($installed_controller['files'])) {

                $update_data['files'] = multi_array_unique(array_merge_recursive($installed_controller['files'], $manifest['contents']));
            } else {

                $update_data['files'] = $manifest['contents'];
            }
        }

        if (!empty($manifest['info']['addon_id'])) {
            $update_data['files'] = (int) $manifest['info']['addon_id'];
        }

        $model->filterEqual('name', $manifest['package']['name'])->updateFiltered('controllers', $update_data);

        return 'controllers';
    }

    private function copyWidgetImageHint($manifest) {

        if (empty($manifest['info']['image_hint'])) {
            return null;
        }

        $file_path = 'package-images/widgets/' . ($manifest['package']['controller'] ? $manifest['package']['controller'] . '_' : '') . $manifest['package']['name'] . '.' . strtolower(pathinfo($manifest['info']['image_hint'], PATHINFO_EXTENSION));

        if (copy($manifest['info']['image_hint'], $this->cms_config->upload_path . $file_path)) {
            return $file_path;
        }

        return null;
    }

    private function widgetInstall($manifest) {

        $model = new cmsModel();

        $model->insert('widgets', [
            'title'           => $manifest['info']['title'],
            'name'            => $manifest['package']['name'],
            'controller'      => $manifest['package']['controller'],
            'author'          => (isset($manifest['author']['name']) ? $manifest['author']['name'] : LANG_CP_PACKAGE_NONAME),
            'url'             => (isset($manifest['author']['url']) ? $manifest['author']['url'] : null),
            'version'         => $manifest['version']['major'] . '.' . $manifest['version']['minor'] . '.' . $manifest['version']['build'],
            'files'           => (!empty($manifest['contents']) ? $manifest['contents'] : null),
            'addon_id'        => (!empty($manifest['info']['addon_id']) ? (int) $manifest['info']['addon_id'] : null),
            'image_hint_path' => $this->copyWidgetImageHint($manifest),
            'is_external'     => 1
        ]);

        return 'widgets';
    }

    private function widgetUpdate($manifest) {

        $model = new cmsModel();

        $update_data = [
            'title'           => $manifest['info']['title'],
            'author'          => (isset($manifest['author']['name']) ? $manifest['author']['name'] : LANG_CP_PACKAGE_NONAME),
            'url'             => (isset($manifest['author']['url']) ? $manifest['author']['url'] : null),
            'image_hint_path' => $this->copyWidgetImageHint($manifest),
            'version'         => $manifest['version']['major'] . '.' . $manifest['version']['minor'] . '.' . $manifest['version']['build']
        ];

        $installed_widget = $model->filterEqual('name', $manifest['package']['name'])->
                filterEqual('controller', $manifest['package']['controller'])->
                getItem('widgets', function ($item) {
            $item['files'] = cmsModel::yamlToArray($item['files']);
            return $item;
        });

        if (!empty($manifest['contents'])) {
            if (!empty($installed_widget['files'])) {

                $update_data['files'] = multi_array_unique(array_merge_recursive($installed_widget['files'], $manifest['contents']));
            } else {

                $update_data['files'] = $manifest['contents'];
            }
        }

        if (!empty($manifest['info']['addon_id'])) {
            $update_data['files'] = (int) $manifest['info']['addon_id'];
        }

        $model->filterEqual('name', $manifest['package']['name'])->
                filterEqual('controller', $manifest['package']['controller'])->
                updateFiltered('widgets', $update_data);

        return 'widgets';
    }

    private function otherInstall($manifest) {

        // Переходная проверка, был баг с определением системного пакета
        if (!empty($manifest['install']['type']) && $manifest['install']['type'] == 'system') {
            return '';
        }

        $count_installed_before = $this->count_installed_before;

        $this->loadInstalledCounts();

        // если установили виджет
        if ($this->count_installed_before['widgets'] > $count_installed_before['widgets']) {

            $this->model->orderBy('id', 'desc')->limit($this->count_installed_before['widgets'] - $count_installed_before['widgets']);

            $widgets_ids = $this->model->selectOnly('id')->get('widgets', function ($item, $model) {
                return $item['id'];
            }, false);

            $this->model->filterIn('id', $widgets_ids)->updateFiltered('widgets', [
                'addon_id'    => (!empty($manifest['info']['addon_id']) ? (int) $manifest['info']['addon_id'] : null),
                'files'       => (!empty($manifest['contents']) ? $manifest['contents'] : null),
                'is_external' => 1
            ], true);
        }

        // если установили компонент
        if ($this->count_installed_before['controllers'] > $count_installed_before['controllers']) {

            $this->model->orderBy('id', 'desc')->limit($this->count_installed_before['controllers'] - $count_installed_before['controllers']);

            $controllers_ids = $this->model->selectOnly('id')->get('controllers', function ($item, $model) {
                return $item['id'];
            }, false);

            $this->model->filterIn('id', $controllers_ids)->updateFiltered('controllers', [
                'addon_id'    => (!empty($manifest['info']['addon_id']) ? (int) $manifest['info']['addon_id'] : null),
                'files'       => (!empty($manifest['contents']) ? $manifest['contents'] : null),
                'is_external' => 1
            ], true);
        }

        return '';
    }

    private function systemInstall($manifest) {
        return '';
    }

    private function systemUpdate($manifest) {
        return '';
    }

    private function runPackageInstaller($file) {

        // нет файла, считаем, что так задумано и ошибку не отдаем
        if (!file_exists($file)) {
            return true;
        }
        @chmod($file, 0666);

        if (!is_readable($file)) {
            return sprintf(LANG_CP_INSTALL_PERM_ERROR, $file);
        }

        include_once $file;

        if (!function_exists('install_package')) {
            return false;
        }

        return call_user_func('install_package');
    }

    private function importPackageDump($file) {

        if (!file_exists($file)) {
            return true;
        }

        @chmod($file, 0666);

        if (!is_readable($file)) {

            cmsUser::addSessionMessage(sprintf(LANG_CP_INSTALL_PERM_ERROR, $file), 'error');

            return false;
        }

        return cmsDatabase::getInstance()->importDump($file);
    }

    private function updateEvents($controller_name) {

        $diff_events = $this->getEventsDifferences($controller_name);

        if ($diff_events['added']) {
            foreach ($diff_events['added'] as $controller => $events) {
                foreach ($events as $event) {
                    $this->model->addEvent($controller, $event);
                }
            }
        }

        if ($diff_events['deleted']) {
            foreach ($diff_events['deleted'] as $controller => $events) {
                foreach ($events as $event) {
                    $this->model->deleteEvent($controller, $event);
                }
            }
        }

        return true;
    }

    private function loadInstalledCounts() {

        $this->model->resetFilters();

        $this->count_installed_before = [
            'widgets'     => $this->model->getCount('widgets', 'id', true),
            'controllers' => $this->model->getCount('controllers', 'id', true)
        ];

        return $this;
    }

}
