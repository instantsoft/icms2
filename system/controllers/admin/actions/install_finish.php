<?php
/**
 * Финальное действие установки
 * Выполняются все SQl запросы из дампа
 * Выполняется функция установки пакета
 */
class actionAdminInstallFinish extends cmsAction {

    public function run() {

        if (!cmsForm::validateCSRFToken($this->request->get('csrf_token', ''))) {

            cmsUser::addSessionMessage(LANG_FORM_ERRORS, 'error');

            return $this->redirectToAction('install');
        }

        if (function_exists('opcache_reset')) {
            @opcache_reset();
        }

        $installer = new cmsInstaller($this->getInstallPackagesPath('root'), $this->controller);

        // id дополнения передаётся при установке из каталога дополнений
        $installer->setManifestAddonId($this->request->get('addon_id', 0));

        $result = $installer->install();

        if ($result === null) {

            cmsUser::addSessionMessage(($installer->getInstallError() ?: LANG_CP_INSTALL_ERROR), 'error');

            return $this->redirectToAction('install');
        }

        $is_cleared = $installer->clear();

        return $this->cms_template->render([
            'is_cleared'      => $is_cleared,
            'undeleted_files' => $installer->getUndeletedFiles(),
            'redirect_action' => $result,
            'path_relative'   => $this->getInstallPackagesPath('rel_root')
        ]);
    }

}
