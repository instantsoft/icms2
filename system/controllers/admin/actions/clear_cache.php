<?php

class actionAdminClearCache extends cmsAction {

    public function run($type) {

        if (!in_array($type, ['css', 'js'])) {
            return cmsCore::error404();
        }

        $cache_folder      = "cache/static/{$type}";
        $cache_folder_path = cmsConfig::get('root_path') . $cache_folder;

        if (files_clear_directory($cache_folder_path)) {

            cmsUser::addSessionMessage(sprintf(LANG_CP_SETTINGS_MERGED_CLEANED, '/' . $cache_folder), 'success');
        } else {

            cmsUser::addSessionMessage(sprintf(LANG_CP_SETTINGS_MERGED_CLEAN_FAIL, '/' . $cache_folder), 'error');
        }

        return $this->redirectBack();
    }

}
