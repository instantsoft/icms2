<?php

class actionFilesDownload extends cmsAction {

    public function run($id, $url_key) {

        $file = $this->model->getFile($id);
        if (!$file) {
            return cmsCore::error404();
        }

        if (files_user_file_hash($file['path']) !== $url_key) {
            return cmsCore::errorForbidden();
        }

        $file = cmsEventsManager::hook('files_before_download', $file);

        $this->model->incrementDownloadsCounter($file['id']);

        return $this->cms_core->response->sendDownloadFile($file['path'], $file['name']);
    }

}
