<?php

class onImagesUserDelete extends cmsAction {

    public function run($user) {

        $dest_dir = $this->cms_config->upload_path . sprintf('%03d', intval($user['id'] / 100)) . '/u' . $user['id'];

        files_remove_directory($dest_dir);

        $user_files = $this->model_files->limit(false)->filterEqual('user_id', $user['id'])->getFiles();

        if($user_files){
            foreach ($user_files as $user_file) {
                $this->model_files->deleteFile($user_file);
            }
        }

        return $user;
    }

}
