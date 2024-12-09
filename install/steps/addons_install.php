<?php

define('SESSION_START', true);

require_once __DIR__ . '/../../bootstrap.php';

$core->initLanguage();

$addons_dirs = files_get_dirs_list(PATH . '/install/externals', true);

$admin = cmsCore::getController('admin');

foreach ($addons_dirs as $addons_dir) {

    $installer = new cmsInstaller(PATH . '/install/externals/' . $addons_dir, $admin);

    $result = $installer->useNamespace()->install();

    if ($result === null) {

        $message = $installer->getInstallError() ?: LANG_CP_INSTALL_ERROR;
    }
}

$core->response->setContent([
    'error' => false,
    'warning' => $message ?? false
])->sendAndExit();
