<?php
/**
 * Копирование файлов пакета в дерево каталогов InstantCMS
 */
class actionAdminInstallFtp extends cmsAction {

    public function run() {

        if (!cmsForm::validateCSRFToken($this->request->get('csrf_token', ''))) {

            cmsUser::addSessionMessage(LANG_FORM_ERRORS, 'error');

            return $this->redirectToAction('install');
        }

        $installer = new cmsInstaller($this->getInstallPackagesPath('root'), $this->controller);

        $manifest = $installer->getManifest();

        if (!$manifest) {
            return $this->redirectToAction('install');
        }

        if (!$manifest['contents']) {
            return $this->redirectToFinish();
        }

        // Если права доступа позволяют, копируем обычным способом
        if ($this->isWritableDestFiles($manifest['contents'])) {

            files_copy_directory($installer->getPackageContentsDir(), $this->cms_config->root_path);

            return $this->redirectToFinish();
        }

        // Иначе, спрашиваем доступы FTP

        $form = $this->getForm('ftp');

        $ftp_account = cmsUser::getUPS('admin.install.ftp');

        $account = cmsUser::sessionGet('ftp_account') ?: ($ftp_account ?: []);
        $account['addon_id'] = $this->request->get('addon_id', 0);

        if ($this->request->has('submit')) {

            $account = array_merge($account, $form->parse($this->request, true, $account));

            cmsUser::setUPS('admin.install.ftp', [
                'host'    => $account['host'],
                'port'    => $account['port'],
                'path'    => $account['path'],
                'is_pasv' => $account['is_pasv']
            ]);

            if ($account['save_to_session']) {

                cmsUser::sessionSet('ftp_account', $account);

            } else {

                cmsUser::sessionUnset('ftp_account');
            }

            $errors = $form->validate($this, $account);

            if ($errors) {
                cmsUser::addSessionMessage(LANG_FORM_ERRORS, 'error');
            }

            if (!$errors) {

                $account['host'] = trim(str_replace('ftp://', '', $account['host']), '/');

                if ($account['path'] !== '/') {

                    $account['path'] = '/' . trim($account['path'], '/') . '/';
                }

                if ($this->uploadPackageToFTP($account, $installer->getPackageContentsDir())) {

                    return $this->redirectToFinish();
                }
            }
        }

        return $this->cms_template->render('install_ftp', [
            'manifest' => $manifest,
            'account'  => $account,
            'form'     => $form,
            'errors'   => $errors ?? false
        ]);
    }

    /**
     * Выполняет редирект на финальный шаг установки
     *
     * @return redirect
     */
    private function redirectToFinish() {
        return $this->redirectToAction('install', ['finish'], [
            'csrf_token' => cmsForm::getCSRFToken(),
            'addon_id'   => $this->request->get('addon_id', 0)
        ]);
    }

    /**
     * Загружает файлы дополнения по FTP
     *
     * @param array $account Массив FTP данных для соединения
     * @param string $src_dir Путь к директории, откуда копируем
     * @return bool
     */
    private function uploadPackageToFTP(array $account, string $src_dir) {

        $connection = @ftp_connect($account['host'], $account['port'], 30);

        if (!$connection) {

            cmsUser::addSessionMessage(LANG_CP_FTP_CONNECT_FAILED, 'error');

            return false;
        }

        $session = @ftp_login($connection, $account['user'], $account['pass']);

        if (!$session) {

            cmsUser::addSessionMessage(LANG_CP_FTP_AUTH_FAILED, 'error');

            return false;
        }

        if ($account['is_pasv']) {

            ftp_set_option($connection, FTP_USEPASVADDRESS, false);

            ftp_pasv($connection, true);
        }

        if (!$this->checkDestination($connection, $account)) {
            return false;
        }

        $dst_dir = '/' . trim($account['path'], '/');

        try {

            $this->uploadDirectoryToFTP($connection, $src_dir, $dst_dir);

        } catch (Exception $e) {

            ftp_close($connection);

            cmsUser::addSessionMessage($e->getMessage(), 'error');

            return false;
        }

        ftp_close($connection);

        return true;
    }

    /**
     * Проверяет, что директория FTP содержит установку InstantCMS
     *
     * @param resource|FTP\Connection $connection
     * @param array $account Массив FTP данных для соединения
     * @return bool
     */
    private function checkDestination($connection, $account) {

        $ftp_path = 'ftp://' . $account['host'] . $account['path'];

        $check_dirs = [
            'system/core'   => 'core.php',
            'system/config' => 'config.php'
        ];

        if (!ftp_nlist($connection, $account['path'])) {

            cmsUser::addSessionMessage(sprintf(LANG_CP_FTP_NO_ROOT, $ftp_path), 'error');

            return false;
        }

        $files_list = [];

        foreach ($check_dirs as $dir => $file) {

            $contents = ftp_nlist($connection, $account['path'] . $dir);

            if (is_array($contents)) {
                foreach ($contents as $item) {
                    $files_list[] = basename($item);
                }
            }

            if (!$files_list || !in_array($file, $files_list)) {

                cmsUser::addSessionMessage(sprintf(LANG_CP_FTP_BAD_ROOT, $ftp_path), 'error');

                return false;
            }
        }

        return true;
    }

    /**
     * Загружает директорию по FTP
     *
     * @param resource|FTP\Connection $conn_id
     * @param string $src_dir Директория, которую нужно загрузить
     * @param string $dst_dir Директория внутри соединения FTP, куда нужно загрузить
     * @throws Exception
     */
    private function uploadDirectoryToFTP($conn_id, string $src_dir, string $dst_dir) {

        $directory = dir($src_dir);
        $can_chmod = function_exists('ftp_chmod');

        while (($file = $directory->read()) !== false) {

            if ($file === '.' || $file === '..') {
                continue;
            }

            $src_path = rtrim($src_dir, '/') . '/' . $file;
            $dst_path = rtrim($dst_dir, '/') . '/' . $file;

            if (is_dir($src_path)) {

                // Директория назначения существует?
                if (!@ftp_chdir($conn_id, $dst_path) && !@ftp_mkdir($conn_id, $dst_path)) {
                    throw new Exception(sprintf('%s: %s', LANG_CP_FTP_MKDIR_FAILED, $dst_path));
                }

                // Права доступа
                if ($can_chmod) {
                    @ftp_chmod($conn_id, 0755, $dst_path);
                }

                // Рекурсивная загрузка поддиректории
                $this->uploadDirectoryToFTP($conn_id, $src_path, $dst_path);

            } else {

                // Загружаем файл
                if (!@ftp_put($conn_id, $dst_path, $src_path, FTP_BINARY)) {
                    throw new Exception(sprintf('%s: %s', LANG_CP_FTP_UPLOAD_FAILED, $dst_path));
                }

                // Права доступа
                if ($can_chmod) {
                    @ftp_chmod($conn_id, 0644, $dst_path);
                }
            }
        }

        $directory->close();
    }

    /**
     * Проверяет дерево директорий InstantCMS на возможность записи
     *
     * @param array $package_contents_list Массив дерева директорий и файлов
     * @param array $no_writable Сюда пишутся пути, недоступные для записи
     * @param bool $start_path Начальная директория
     * @return bool
     */
    private function isWritableDestFiles(array $package_contents_list, &$no_writable = [], $start_path = false) {

        if (!$start_path) {
            $start_path = $this->cms_config->root_path;
        }

        clearstatcache($start_path);

        foreach ($package_contents_list as $file => $files) {

            $path = $start_path . $file;

            if (is_dir($path) || is_file($path)) {

                if (!$this->isCreateOrOverwriteFile($path)) {
                    $no_writable[] = $path;
                }
            }

            if (is_array($files)) {

                $this->isWritableDestFiles($files, $no_writable, $path . '/');

            } else {

                $path = $start_path . $files;

                if (!$this->isCreateOrOverwriteFile($path)) {
                    $no_writable[] = $path;
                }
            }
        }

        return count($no_writable) ? false : true;
    }

    /**
     * Проверяет возможность создания или перезаписи файла/директории
     *
     * @param string $path
     * @return bool
     */
    private function isCreateOrOverwriteFile(string $path) {

        if (file_exists($path)) {
            return is_writable($path);
        }

        // Проверяем возможность записи в родительскую директорию
        $parent_dir = dirname($path);

        return is_writable($parent_dir) || $this->isCreateOrOverwriteFile($parent_dir);
    }

}
