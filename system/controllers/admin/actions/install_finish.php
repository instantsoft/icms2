<?php
/**
 * Финальное действие установки
 * Выполняются все SQl запросы из дампа
 * Выполняется функция установки пакета
 */
class actionAdminInstallFinish extends cmsAction {

    use \icms\controllers\admin\traits\packageInstallerTrait;

    public function run() {

        if (!cmsForm::validateCSRFToken($this->request->get('csrf_token', ''))) {

            cmsUser::addSessionMessage(LANG_FORM_ERRORS, 'error');

            return $this->redirectToAction('install');
        }

        if (function_exists('opcache_reset')) {
            @opcache_reset();
        }

        // Совместимость 2.18.1-2.18.2 чтобы пакет обновления cms в ошибку не ушёл
        // т.к. в пакете обновления до 2.18.2 нет ни дампов, ни install.php
        // просто считаем установку успешной
        $ini_file_default = $this->getInstallPackagesPath('root') . '/manifest.ru.ini';
        if (file_exists($ini_file_default)) {

            $is_cleared = files_clear_directory($this->getInstallPackagesPath('root'));

            return $this->cms_template->render([
                'is_cleared'      => $is_cleared,
                'undeleted_files' => [],
                'redirect_action' => '',
                'path_relative'   => $this->getInstallPackagesPath('rel_root')
            ]);
        }

        $package_data = $this->getPackageFileData();
        if (!$package_data) {

            cmsUser::addSessionMessage(LANG_CP_INSTALL_ERROR, 'error');

            return $this->redirectToAction('install');
        }

        $source_install_package_path = $this->extractPackage($package_data['path']);
        if (!is_dir($source_install_package_path)) {

            cmsUser::addSessionMessage(LANG_CP_INSTALL_ZIP_ERROR . (': ' . $source_install_package_path), 'error');

            return $this->redirectToAction('install');
        }

        $installer = new cmsInstaller($source_install_package_path, $this->controller);

        $manifest = $installer->getManifest();
        if (!$manifest) {
            return $this->redirectToAction('install');
        }

        // id дополнения передаётся при установке из каталога дополнений
        $installer->setManifestAddonId($this->request->get('addon_id', 0));

        // Если есть форма опций установщика, показываем её
        $install_options = $this->displayAndGetPackageOptions($installer);
        // Если форма опций есть
        if ($install_options !== null) {
            // Пришла строка, значит это шаблон, выводим его
            if (is_string($install_options)) {
                return $install_options;
            }
            // Если пришёл массив, значит это уже данные из формы
        }

        $result = $installer->install($install_options ?? []);

        if ($result === null) {

            cmsUser::addSessionMessage(($installer->getInstallError() ?: LANG_CP_INSTALL_ERROR), 'error');

            return $this->redirectToAction('install');
        }

        $is_cleared = $this->cleanupPackageFile();

        return $this->cms_template->render([
            'is_cleared'      => $is_cleared,
            'undeleted_files' => $installer->getUndeletedFiles(),
            'redirect_action' => $result,
            'path_relative'   => $this->getInstallPackagesPath('rel_root')
        ]);
    }

    private function displayAndGetPackageOptions(cmsInstaller $installer) {

        $form = $installer->getPackageOptionsForm();
        if (!$form) {
            return null;
        }

        $manifest = $installer->getManifest();

        $form->addField($form->getLastFieldsetId(), new fieldHidden('addon_id', [
            'default' => $manifest['info']['addon_id']
        ]));

        if ($this->request->has('submit')) {

            $install_options = $form->parse($this->request, true);

            $errors = $form->validate($this, $install_options);

            if (!$errors) {
                return $install_options;
            }

            cmsUser::addSessionMessage(LANG_FORM_ERRORS, 'error');
        }

        return $this->cms_template->render('install_package_options', [
            'manifest' => $manifest,
            'data'     => $install_options ?? [],
            'form'     => $form,
            'errors'   => $errors ?? false
        ]);
    }

}
