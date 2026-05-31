<?php
/**
 * Единая точка входа в установку дополнений
 * Показывается форма загрузки пакета
 * Загружается и распаковывается архив
 * Показывается информация о пакете
 */
class actionAdminInstall extends cmsAction {

    use \icms\controllers\admin\traits\packageInstallerTrait;

    private $upload_name = 'package';
    private $upload_exts = 'zip';
    private $upload_path = '';

    public function run($do = false) {

        // если нужно, передаем управление другому экшену
        if ($do) {
            return $this->runExternalActionIfExists('install_' . $do, array_slice($this->params, 1));
        }

        // Удаляем архив, если остался
        $this->cleanupPackageFile();

        $this->upload_path = $this->getInstallPackagesPath('root');

        $package_name = $this->uploadPackage();

        if (!$package_name) {
            return $this->showUploadForm();
        }

        return $this->showPackageInfo($package_name);
    }

    /**
     * Распаковывает и показывает страницу информации о дополнении
     *
     * @param string $package_name Имя файла загруженного архива
     * @return redirect|html
     */
    private function showPackageInfo(string $package_name) {

        $source_install_package_path = $this->extractPackage($this->upload_path . '/' . $package_name);

        if (!is_dir($source_install_package_path)) {

            cmsUser::addSessionMessage(LANG_CP_INSTALL_ZIP_ERROR . (': ' . $source_install_package_path), 'error');

            return $this->redirectToAction('install');
        }

        $installer = new cmsInstaller($source_install_package_path, $this->controller);

        $manifest = $installer->getManifest();

        if (!$manifest) {
            return $this->redirectToAction('install');
        }

        // если пакет уже установлен, а мы пытаемся его еще раз установить, показываем сообщение
        if (!empty($manifest['package']['installed_version']) && $manifest['package']['action'] === 'install') {

            cmsUser::addSessionMessage(sprintf(LANG_CP_PACKAGE_DUBLE_INSTALL, $manifest['package']['installed_version']), 'error');

            return $this->redirectToAction('install');
        }

        // если это пакет обновления, а полная версия не установлена
        if ($manifest['package'] && empty($manifest['package']['installed_version']) && $manifest['package']['action'] === 'update') {

            cmsUser::addSessionMessage(LANG_CP_PACKAGE_UPDATE_NOINSTALL, 'error');

            return $this->redirectToAction('install');
        }

        // если это пакет обновления и обновляемая версия ниже существующей или равна
        if (!empty($manifest['package']['installed_version']) && $manifest['package']['action'] === 'update') {

            if (version_compare($manifest['version_str'], $manifest['package']['installed_version']) == -1) {

                cmsUser::addSessionMessage(sprintf(
                    LANG_CP_PACKAGE_UPDATE_ERROR,
                    $manifest['package']['type_hint'],
                    $manifest['info']['title'],
                    $manifest['version_str'],
                    $manifest['package']['installed_version']
                ), 'error');

                return $this->redirectToAction('install');
            }

            if (version_compare($manifest['version_str'], $manifest['package']['installed_version']) == 0) {

                cmsUser::addSessionMessage(LANG_CP_PACKAGE_UPDATE_IS_UPDATED, 'error');

                return $this->redirectToAction('install');
            }
        }

        return $this->cms_template->render('install_package_info', [
            'manifest' => $manifest,
            'addon_id' => $this->request->get('addon_id', 0)
        ]);
    }

    /**
     * Показывает форму загрузки
     *
     * @return html
     */
    private function showUploadForm() {

        return $this->cms_template->render('install_upload', [
            'errors'           => $this->checkErrors(),
            'addon_id'         => $this->request->get('addon_id', 0),
            'install_rel_root' => $this->getInstallPackagesPath('rel_root')
        ]);
    }

    /**
     * Проверяет требования для работы с дополнениями перед загрузкой
     *
     * @return array
     */
    private function checkErrors() {

        $installer_upload_rel = $this->getInstallPackagesPath('rel_root');

        $errors = [];

        if (!cmsCore::isWritable($this->upload_path)) {
            $errors[] = [
                'text'       => sprintf(LANG_CP_INSTALL_NOT_WRITABLE, $installer_upload_rel),
                'hint'       => LANG_CP_INSTALL_NOT_WRITABLE_HINT,
                'fix'        => LANG_CP_INSTALL_NOT_WRITABLE_FIX
            ];
        }

        if (!class_exists('ZipArchive')) {
            $errors[] = [
                'text'       => LANG_CP_INSTALL_NOT_ZIP,
                'hint'       => LANG_CP_INSTALL_NOT_ZIP_HINT,
                'fix'        => LANG_CP_INSTALL_NOT_ZIP_FIX,
                'workaround' => sprintf(LANG_CP_INSTALL_NOT_ZIP_WA, $installer_upload_rel)
            ];
        }

        if (!function_exists('parse_ini_file')) {
            $errors[] = [
                'text' => LANG_CP_INSTALL_NOT_PARSE_INI_FILE,
                'hint' => LANG_CP_INSTALL_NOT_PARSE_INI_FILE_HINT,
                'fix'  => LANG_CP_INSTALL_NOT_PARSE_INI_FILE_FIX
            ];
        }

        return $errors;
    }

    /**
     * Загружает архив дополнения из формы
     *
     * @return string Имя загруженного файла
     */
    private function uploadPackage() {

        // Разрешаем загрузку по ссылке только со своих доменов
        $this->cms_uploader->enableRemoteUpload()->
                setAllowedRemoteHosts(['instantcms.ru', 'api.instantcms.ru', 'addons.instantcms.ru', 'upd.instantcms.ru']);

        if (!$this->cms_uploader->isUploaded($this->upload_name) && !$this->cms_uploader->isUploadedFromLink($this->upload_name)) {

            $last_error = $this->cms_uploader->getLastError();
            if ($last_error) {
                cmsUser::addSessionMessage($last_error, 'error');
            }

            return '';
        }

        if (!cmsForm::validateCSRFToken($this->request->get('csrf_token', ''))) {

            cmsUser::addSessionMessage(LANG_FORM_ERRORS, 'error');

            return '';
        }

        files_clear_directory($this->upload_path);

        // zip файлы, не более 50Мб
        $result = $this->cms_uploader->setAllowedMime([
                    'application/zip'
                ])->setFileName(string_random())->
                upload($this->upload_name, $this->upload_exts, 52428800, basename($this->upload_path));

        if (!$result['success']) {
            cmsUser::addSessionMessage($result['error'], 'error');
            return '';
        }

        $new_result = $this->registerPackageFile($result['path']);

        if (!$new_result) {

            @unlink($result['path']);

            return '';
        }

        return $new_result['name'];
    }

}
