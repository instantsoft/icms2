<?php
    /**
     * Веб-версия кастомизации архива InstantCMS с Vue.js и Bootstrap 4
     * Работает через AJAX, без перезагрузки страницы
     */

    define('PATH', dirname(__FILE__) . '/');
    define('PATH_ICMS', dirname(PATH) . '/');

    include_once PATH . 'functions.php';
    include_once PATH_ICMS . 'system/libs/files.helper.php';

    $version_file = PATH_ICMS . 'system/config/version.ini';
    $version = parse_ini_file($version_file);
    $core_version = [
        'date'    => $version['date'],
        'version' => $version['major'] . '.' . $version['minor'] . '.' . $version['build']
    ];

    $all_langs = get_langs();
    define('LANG', 'en');
    include PATH . 'languages/' . LANG . '/language.php';

    $components = get_files_list(PATH . 'manifests', '*.php', true);

    // Обработка AJAX-запроса
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest') {
        header('Content-Type: application/json');

        function removeDirectory($dir) {
            if (!file_exists($dir)) return true;
            if (!is_dir($dir)) return unlink($dir);
            foreach (scandir($dir) as $item) {
                if ($item == '.' || $item == '..') continue;
                if (!removeDirectory($dir . DIRECTORY_SEPARATOR . $item)) return false;
            }
            return rmdir($dir);
        }

        function createZip($source, $destination) {
            $zip = new ZipArchive();
            if ($zip->open($destination, ZipArchive::CREATE | ZipArchive::OVERWRITE) === true) {
                $files = new RecursiveIteratorIterator(
                    new RecursiveDirectoryIterator($source),
                    RecursiveIteratorIterator::LEAVES_ONLY
                );
                foreach ($files as $file) {
                    if (!$file->isDir()) {
                        $filePath = $file->getRealPath();
                        $relativePath = substr($filePath, strlen($source) + 1);
                        $zip->addFile($filePath, $relativePath);
                    }
                }
                return $zip->close();
            }
            return false;
        }

        $data = json_decode(file_get_contents('php://input'), true);
        $manifest = [
            'removed' => $data['components'] ?? [],
            'create_archive' => $data['createZip'] ?? false
        ];

        $output = [];
        if (!empty($manifest['removed'])) {
            foreach ($manifest['removed'] as $controller_name) {
                $output[] = "Удаление компонента: $controller_name";
                $controller_manifest_path = PATH . 'manifests/' . $controller_name . '.php';
                if (is_readable($controller_manifest_path)) {
                    $controller_manifest = include $controller_manifest_path;
                    delete_manifest_files($controller_manifest);
                }
                foreach ($all_langs as $lang) {
                    removeDirectory(PATH . 'languages/' . $lang . '/sql/packages/' . $controller_name);
                }
            }
        }

        $output[] = "Очистка системных файлов";
        removeDirectory(PATH_ICMS . '.git/');
        removeDirectory(PATH_ICMS . '.github/');
        removeDirectory(PATH_ICMS . 'update/');
        @unlink(PATH_ICMS . 'LICENSE');
        @unlink(PATH_ICMS . 'README.md');

        $output[] = "Установка прав доступа";
        $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator(PATH_ICMS));
        foreach ($iterator as $file) {
            if ($file->isFile()) chmod($file->getPathname(), 0644);
            elseif ($file->isDir()) chmod($file->getPathname(), 0755);
        }
        chmod(PATH_ICMS . 'system/config', 0777);
        chmod(PATH_ICMS . 'cache', 0777);
        chmod(PATH_ICMS . 'upload', 0777);

        file_put_contents($version_file, "\nis_custom = 1", FILE_APPEND);

        $archive_link = '';
        if ($manifest['create_archive']) {
            $output[] = "Создание архива";
            $archive_name = "instantcms_{$core_version['date']}_v{$core_version['version']}-custom.zip";
            $archive_path = dirname(PATH_ICMS) . '/' . $archive_name;
            if (createZip(PATH_ICMS, $archive_path)) {
                $output[] = "Архив успешно создан";
                $archive_link = '/' . $archive_name; // Относительный путь для скачивания
            } else {
                $output[] = "Ошибка при создании архива";
            }
        }

        $output[] = "Процесс завершен!";
        echo json_encode(['status' => 'success', 'messages' => $output, 'archive_link' => $archive_link]);
        exit;
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>InstantCMS Rebuild v<?php echo $core_version['version']; ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <style>
        #output { margin-top: 20px; }
        .success { color: #28a745; }
        .error { color: #dc3545; }
    </style>
</head>
<body>
<div id="app" class="container mt-5">
    <h1 class="mb-4">InstantCMS Rebuild v<?php echo $core_version['version']; ?></h1>

    <div class="card mb-4">
        <div class="card-header">Выберите компоненты для удаления</div>
        <div class="card-body">
            <div v-for="component in components" :key="component" class="form-check">
                <input class="form-check-input" type="checkbox" :value="component" v-model="selectedComponents" :id="component">
                <label class="form-check-label" :for="component">{{ component }}</label>
            </div>
        </div>
    </div>

    <div class="form-group">
        <label>Создать ZIP-архив?</label>
        <div class="form-check">
            <input class="form-check-input" type="radio" v-model="createZip" value="true" id="createZipYes">
            <label class="form-check-label" for="createZipYes">Да</label>
        </div>
        <div class="form-check">
            <input class="form-check-input" type="radio" v-model="createZip" value="false" id="createZipNo" checked>
            <label class="form-check-label" for="createZipNo">Нет</label>
        </div>
    </div>

    <button class="btn btn-primary" @click="rebuild" :disabled="isProcessing">Запустить сборку</button>

    <div id="output" v-if="messages.length">
        <h3>Результат:</h3>
        <p v-for="(message, index) in messages" :key="index" :class="messageClass(message)">
            {{ message }}
        </p>
        <a v-if="archiveLink" :href="archiveLink" class="btn btn-success mt-2">Скачать архив</a>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/vue@2.6.14/dist/vue.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/axios@0.21.1/dist/axios.min.js"></script>
<script>
    new Vue({
        el: '#app',
        data: {
            components: <?php echo json_encode($components); ?>,
            selectedComponents: [],
            createZip: 'false',
            isProcessing: false,
            messages: [],
            archiveLink: ''
        },
        methods: {
            rebuild() {
                this.isProcessing = true;
                this.messages = [];
                this.archiveLink = '';

                axios.post('', {
                    components: this.selectedComponents,
                    createZip: this.createZip === 'true'
                }, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                })
                    .then(response => {
                        this.messages = response.data.messages;
                        this.archiveLink = response.data.archive_link;
                    })
                    .catch(error => {
                        this.messages = ['Ошибка: ' + (error.response?.data?.message || error.message)];
                    })
                    .finally(() => {
                        this.isProcessing = false;
                    });
            },
            messageClass(message) {
                if (message.includes('Ошибка')) return 'error';
                if (message.includes('успешно')) return 'success';
                return '';
            }
        }
    });
</script>
</body>
</html>