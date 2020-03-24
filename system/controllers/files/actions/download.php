<?php

class actionFilesDownload extends cmsAction {

    public function run($id, $url_key){

        $file = $this->model->getFile($id);
        if (!$file) { return cmsCore::error404(); }

        if (files_user_file_hash($file['path']) != $url_key) { cmsCore::errorForbidden(); }

        $file = cmsEventsManager::hook('files_before_download', $file);

        $filename = cmsConfig::get('upload_path') . $file['path'];
        $original_filename = $file['name'];

        $this->model->incrementDownloadsCounter($file['id']);

        header("Content-Type: application/force-download");
        header("Content-Length: " . filesize($filename));
        header('Content-Disposition: attachment; filename="' . $original_filename . '"');

        if(ob_get_length()) { ob_end_clean(); }

        readfile($filename);

        $this->halt();

    }

}
