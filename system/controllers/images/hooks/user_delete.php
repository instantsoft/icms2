<?php

class onImagesUserDelete extends cmsAction {

    public function run($user){

        $dest_dir = $this->cms_config->upload_path . sprintf('%03d', intval($user['id']/100)).'/u'.$user['id'];

        files_remove_directory($dest_dir);

        return $user;

    }

}
