<?php
/**
 * Единая точка входа в установку дополнений
 * Показывается форма загрузки пакета
 * Загружается и распаковывается архив
 * Показывается информация о пакете
 */
class actionAdminInstall extends cmsAction {

    private $upload_name = 'package';
    private $upload_exts = 'zip';
    private $upload_path = '';

    public function run($do = false) {

        // если нужно, передаем управление другому экшену
        if ($do) {
            $this->runExternalAction('install_' . $do, array_slice($this->params, 1));
            return;
        }

        $this->upload_path = $this->getInstallPackagesPath('root');

        $package_name  = $this->request->get('package_name', $this->uploadPackage());
        $is_no_extract = $this->request->get('is_no_extract', false);

        if (!$is_no_extract && !$package_name) {
            return $this->showUploadForm();
        }

        return $this->showPackageInfo($package_name, $is_no_extract);
    }

    /**
     * Распаковывает и показывает страницу информации о дополнении
     *
     * @param string $package_name Имя файла загруженного архива
     * @param bool $is_no_extract Не распаковывать архив (если уже распакован)
     * @return redirect|html
     */
    private function showPackageInfo($package_name, $is_no_extract = false) {

        if (!$is_no_extract) {
            if (true !== ($zip_result = $this->extractPackage($package_name))) {

                cmsUser::addSessionMessage(LANG_CP_INSTALL_ZIP_ERROR . ($zip_result ? ': ' . $zip_result : ''), 'error');

                return $this->redirectToAction('install');
            }
        }

        $installer = new cmsInstaller($this->upload_path, $this->controller);

        $manifest = $installer->getManifest();

        if (!$manifest) {
            return $this->redirectToAction('install');
        }

        // если пакет уже установлен, а мы пытаемся его еще раз установить, показываем сообщение
        if (!empty($manifest['package']['installed_version']) && $manifest['package']['action'] === 'install') {

            $installer->clear();

            cmsUser::addSessionMessage(sprintf(LANG_CP_PACKAGE_DUBLE_INSTALL, $manifest['package']['installed_version']), 'error');

            return $this->redirectToAction('install');
        }

        // если это пакет обновления, а полная версия не установлена
        if ($manifest['package'] && empty($manifest['package']['installed_version']) && $manifest['package']['action'] === 'update') {

            $installer->clear();

            cmsUser::addSessionMessage(LANG_CP_PACKAGE_UPDATE_NOINSTALL, 'error');

            return $this->redirectToAction('install');
        }

        // если это пакет обновления и обновляемая версия ниже существующей или равна
        if (!empty($manifest['package']['installed_version']) && $manifest['package']['action'] === 'update') {

            if (version_compare($manifest['version_str'], $manifest['package']['installed_version']) == -1) {

                $installer->clear();

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

                $installer->clear();

                cmsUser::addSessionMessage(LANG_CP_PACKAGE_UPDATE_IS_UPDATED, 'error');

                return $this->redirectToAction('install');
            }
        }

        return $this->cms_template->render('install_package_info', [
            'manifest'         => $manifest,
            'addon_id'         => $this->request->get('addon_id', 0),
            'install_url_root' => $this->getInstallPackagesPath('url')
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
                'fix'        => LANG_CP_INSTALL_NOT_WRITABLE_FIX,
                'workaround' => sprintf(LANG_CP_INSTALL_NOT_WRITABLE_WA, $installer_upload_rel)
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
     * Распаковывает архив с дополнением
     *
     * @param string $package_name Имя архива
     * @return string|bool
     */
    private function extractPackage($package_name){

        $zip_file = $this->upload_path . '/' . $package_name;

        $zip = new ZipArchive();

        $res = $zip->open($zip_file);

        if ($res !== true) {

            if (defined('LANG_ZIP_ERROR_' . $res)) {
                $zip_error = constant('LANG_ZIP_ERROR_' . $res);
            } else {
                $zip_error = '';
            }

            return $zip_error;
        }

        $zip->extractTo($this->upload_path);
        $zip->close();

        unlink($zip_file);

        return true;
    }

    /**
     * Загружает архив дополнения из формы
     *
     * @return string|bool false или имя загруженного файла
     */
    private function uploadPackage() {

        $this->cms_uploader->enableRemoteUpload()->setAllowedRemoteHosts(['instantcms.ru', 'api.instantcms.ru', 'addons.instantcms.ru']);

        if (!$this->cms_uploader->isUploaded($this->upload_name) && !$this->cms_uploader->isUploadedFromLink($this->upload_name)) {

            $last_error = $this->cms_uploader->getLastError();
            if ($last_error) {
                cmsUser::addSessionMessage($last_error, 'error');
            }

            return false;
        }

        if (!cmsForm::validateCSRFToken($this->request->get('csrf_token', ''))) {

            cmsUser::addSessionMessage(LANG_FORM_ERRORS, 'error');

            return false;
        }

        files_clear_directory($this->upload_path);

        $result = $this->cms_uploader->setAllowedMime([
            'application/zip'
        ])->upload($this->upload_name, $this->upload_exts, 0, basename($this->upload_path));

        if (!$result['success']) {
            cmsUser::addSessionMessage($result['error'], 'error');
            return false;
        }

        return $result['name'];
    }

}
