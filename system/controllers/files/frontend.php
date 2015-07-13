<?php
class files extends cmsFrontend {

//============================================================================//
//============================================================================//

    public function actionDownload($id, $url_key){

        if (!$id || !$url_key) { return cmsCore::error404(); }

        $file = $this->model->getFile($id);

        if ($file['url_key'] != $url_key) { cmsCore::error404(); }

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

//============================================================================//
//============================================================================//

}
