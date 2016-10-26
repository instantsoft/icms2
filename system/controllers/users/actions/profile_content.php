<?php

class actionUsersProfileContent extends cmsAction {

    public $lock_explicit_call = true;

    public function run($profile, $ctype_name=false, $folder_id=false){

        if (!$ctype_name) { cmsCore::error404(); }

        $content_controller = cmsCore::getController('content', $this->request);

        $ctype = $content_controller->model->getContentTypeByName($ctype_name);
        if (!$ctype) { cmsCore::error404(); }

        if (!$this->cms_user->isPrivacyAllowed($profile, 'view_user_'.$ctype['name'])){
            cmsCore::error404();
        }

        $folders = array();

        if ($ctype['is_folders']){

            $folders = $content_controller->model->getContentFolders($ctype['id'], $profile['id']);

            if ($folders){
                if ($folder_id && array_key_exists($folder_id, $folders)){
                    $content_controller->model->filterEqual('folder_id', $folder_id);
                }
            }

        }

        $content_controller->model->filterEqual('user_id', $profile['id']);

        list($folders, $content_controller->model, $profile, $folder_id) = cmsEventsManager::hook("user_content_{$ctype['name']}_folders", array(
            $folders,
            $content_controller->model,
            $profile,
            $folder_id
        ));

        if ($folders){
            $folders = array('0' => array('id' => '0', 'title' => LANG_ALL)) + $folders;
        }

        if ($this->cms_user->id != $profile['id'] && !$this->cms_user->is_admin){
            $content_controller->model->filterHiddenParents();
        }

        if ($this->cms_user->id == $profile['id'] || $this->cms_user->is_admin){
            $content_controller->model->disableApprovedFilter();
			$content_controller->model->disablePubFilter();
			$content_controller->model->disablePrivacyFilter();
        }

        // указываем тут сортировку, чтобы тут же указать индекс для использования
        $content_controller->model->orderBy('date_pub', 'desc')->forceIndex('user_id');

        list($ctype, $profile) = cmsEventsManager::hook('content_before_profile', array($ctype, $profile));

        if ($folder_id){
            $page_url = href_to('users', $profile['id'], array('content', $ctype_name, $folder_id));
        } else {
            $page_url = href_to('users', $profile['id'], array('content', $ctype_name));
        }

        $list_html = $content_controller->renderItemsList($ctype, $page_url, false, 0, array('user_id' => $profile['id']));

        return $this->cms_template->render('profile_content', array(
            'user'      => $this->cms_user,
            'id'        => $profile['id'],
            'profile'   => $profile,
            'ctype'     => $ctype,
            'folders'   => $folders,
            'folder_id' => $folder_id,
            'html'      => $list_html
        ));

    }

}
