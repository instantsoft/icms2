<?php

namespace icms\controllers\admin\traits;

use ZipArchive, RuntimeException, cmsUser;

/**
 * Трейт для извлечения архивов дополнений
 */
trait packageInstallerTrait {

    /**
     * Возвращает разные пути директории загрузки пакетов
     *
     * @param string $type root | rel_root | url
     * @return string
     */
    public function getInstallPackagesPath(string $type = 'root') {
        return [
            'root'     => $this->cms_config->upload_path . 'installer',
            'rel_root' => $this->cms_config->upload_root . 'installer',
            'url'      => $this->cms_config->upload_host . '/installer'
        ][$type];
    }

    /**
     * Генерирует рандомное имя файла,
     * Переименовывает файл в него,
     * Возвращает имя и хэш файла
     *
     * @param string $zip_file
     * @return ?array
     */
    protected function registerPackageFile(string $zip_file) {

        $file_id = \string_random();
        $file_name = $file_id . '.zip';

        $destination_dir = dirname($zip_file);

        $zip_file_new = $destination_dir . DIRECTORY_SEPARATOR . $file_name;

        if (!rename($zip_file, $zip_file_new)) {
            return null;
        }

        $data = [
            'id'   => $file_id,
            'name' => $file_name,
            'path' => $zip_file_new,
            'hash' => hash_file('sha256', $zip_file_new)
        ];

        cmsUser::sessionSet('installer_package', $data);

        return $data;
    }

    /**
     * Возвращает массив данных файла пакета установки
     * Проверяя при этом хэш файла
     *
     * @return ?array
     */
    protected function getPackageFileData() {

        $data = cmsUser::sessionGet('installer_package');

        if (empty($data['path'])) {
            return null;
        }

        if (hash_file('sha256', $data['path']) !== $data['hash']) {

            cmsUser::sessionUnset('installer_package');

            @unlink($data['path']);

            return null;
        }

        return $data;
    }

    /**
     * Удаляет массив данных файла пакета установки
     * И удаляет сам zip файл пакета
     *
     * @return null
     */
    protected function cleanupPackageFile() {

        $data = cmsUser::sessionGet('installer_package', true);

        if (empty($data['path'])) {
            return null;
        }

        return @unlink($data['path']);
    }

    /**
     * Распаковывает архив с дополнением
     *
     * @param string $zip_file Полный путь к zip файлу пакета
     * @return string|bool
     */
    protected function extractPackage(string $zip_file){

        if (!is_file($zip_file)) {
            return \LANG_ZIP_ERROR_9;
        }

        return $this->extractPackageToTemporaryDirectory($zip_file);
    }

    /**
     * Распаковывает ZIP архив во временную директорию
     * И проверяет валидность
     *
     * @param string $zip_file Имя архива
     * @return string
     * @throws RuntimeException
     */
    protected function extractPackageToTemporaryDirectory(string $zip_file) {

        $zip = new ZipArchive();

        $res = $zip->open($zip_file);

        if ($res !== true) {

            if (defined('LANG_ZIP_ERROR_' . $res)) {
                return constant('LANG_ZIP_ERROR_' . $res);
            }

            return 'Zip open error';
        }

        // Лимиты
        $max_files      = 5000;             // Всего файлов в архиве
        $max_total_size = 50 * 1024 * 1024; // 50 MB макс размер всех файлов
        $max_file_size  = 5 * 1024 * 1024;  // 5 MB на один файл

        if ($zip->numFiles > $max_files) {
            $zip->close();
            return \LANG_ZIP_ERROR_TOO_MANY;
        }

        $tmp_root = ini_get('upload_tmp_dir');
        if (!$tmp_root) {
            $tmp_root = sys_get_temp_dir();
        }

        $tmp_root = realpath($tmp_root);

        if (!$tmp_root) {
            return \LANG_UPLOAD_ERR_NO_TMP_DIR;
        }

        if (!is_writable($tmp_root)) {
            return sprintf(\LANG_CP_INSTALL_NOT_WRITABLE, $tmp_root);
        }

        $tmp_dir = $tmp_root . DIRECTORY_SEPARATOR . 'cms_install_' . \string_random();

        if (!mkdir($tmp_dir, 0755, true) && !is_dir($tmp_dir)) {
            $zip->close();
            return 'Cannot create temporary directory';
        }

        // Запрещённые файлы
        $forbidden_files = [
            '.htaccess',
            '.user.ini'
        ];

        $total_uncompressed_size = 0;

        try {

            for ($i = 0; $i < $zip->numFiles; $i++) {

                $stat = $zip->statIndex($i);
                if (!$stat) {
                    throw new RuntimeException(\LANG_ZIP_ERROR_11);
                }

                $entry_name = ltrim(str_replace('\\', '/', $stat['name']), '/');
                if ($entry_name === '') {
                    throw new RuntimeException('Invalid archive entry');
                }

                // Null byte
                if (strpos($entry_name, "\0") !== false) {
                    throw new RuntimeException('Null byte detected');
                }

                $parts = explode('/', $entry_name);
                foreach ($parts as $part) {
                    if ($part === '..' || $part === '.') {
                        throw new RuntimeException('Path traversal detected');
                    }
                }

                $file_size = (int)$stat['size'];
                if ($file_size > $max_file_size) {
                    throw new RuntimeException('Archive file too large');
                }

                $total_uncompressed_size += $file_size;

                if ($total_uncompressed_size > $max_total_size) {
                    throw new RuntimeException('Archive too large');
                }

                $base_name = strtolower(basename($entry_name));
                if (in_array($base_name, $forbidden_files, true)) {
                    throw new RuntimeException('Forbidden file detected: ' . $base_name);
                }

                // Символическая ссылка
                $opsys = $stat['opsys'] ?? null;
                if ($opsys === ZipArchive::OPSYS_UNIX) {

                    $mode = (($stat['external_attributes']??0) >> 16) & 0xF000;

                    // UNIX symlink
                    if ($mode === 0xA000) {
                        throw new RuntimeException('Symlinks are forbidden');
                    }
                }

                $destination = $tmp_dir . DIRECTORY_SEPARATOR . $entry_name;

                $destination_dir = dirname($destination);

                if (!is_dir($destination_dir)) {
                    if (!mkdir($destination_dir, 0755, true) && !is_dir($destination_dir)) {
                        throw new RuntimeException('Cannot create directory');
                    }
                }

                // Проверка от escape через symlink
                $destination_dir_real = realpath($destination_dir);
                if ($destination_dir_real === false) {
                    throw new RuntimeException('Cannot resolve destination path');
                }

                if (
                    $destination_dir_real !== $tmp_dir &&
                    strpos($destination_dir_real, $tmp_dir . DIRECTORY_SEPARATOR) !== 0
                ) {
                    throw new RuntimeException('Extraction escaped target directory');
                }

                // Директория
                if (substr($entry_name, -1) === '/') {
                    continue;
                }

                // Извлекаем файл
                $input = $zip->getStream($stat['name']);
                if (!$input) {
                    throw new RuntimeException(\LANG_ZIP_ERROR_11 . ' ' . $stat['name']);
                }

                $output = fopen($destination, 'xb');
                if (!$output) {
                    fclose($input);
                    throw new RuntimeException('Cannot create destination file');
                }

                if (stream_copy_to_stream($input, $output) === false) {

                    fclose($input);
                    fclose($output);

                    throw new RuntimeException('Cannot extract file');
                }

                fclose($input);
                fclose($output);

                @chmod($destination, 0644);
            }

            $zip->close();

            return $tmp_dir;

        } catch (\Throwable $e) {

            $zip->close();

            \files_remove_directory($tmp_dir);

            return $e->getMessage();
        }
    }

}
