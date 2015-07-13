<?php

class onImagesUserDelete extends cmsAction {

    public function run($user){

        $cfg = cmsConfig::getInstance();

        $dest_dir = $cfg->upload_path . "u{$user['id']}";

        files_remove_directory($dest_dir);

        return $user;

    }

}
