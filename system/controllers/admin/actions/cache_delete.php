<?php

class actionAdminCacheDelete extends cmsAction {

    public function run($method) {

        if (!in_array($method, ['files', 'memory', 'memcached'])) {
            return cmsCore::error404();
        }

        if ($this->cms_cache->clean()) {

            cmsUser::addSessionMessage(LANG_CP_SETTINGS_CACHE_CLEAN_SUCCESS, 'success');
        } else {

            cmsUser::addSessionMessage(LANG_CP_SETTINGS_CACHE_CLEAN_FAIL, 'error');
        }

        return $this->redirectBack();
    }

}
