<?php

class onPhotosContentAlbumsItemsHtml extends cmsAction {

    public function run($data){

        list($type, $ctype, $profile, $current_folder) = $data;

        if($type == 'user_view'){
            return $this->getUserViewHtml($ctype, $profile, $current_folder);
        }

        return false;

    }

    private function getUserViewHtml($ctype, $profile, $current_folder) {

        $this->model->orderBy($this->options['ordering'], 'desc');

        if (cmsUser::isAllowed('albums', 'view_all') || $this->cms_user->id == $profile['id']) {
            $this->model->disablePrivacyFilter();
            $this->model->disableApprovedFilter();
        }

        if($this->cms_user->isFriend($profile['id'])){
            $this->model->disablePrivacyFilterForFriends();
        }

        $profile['url_params'] = array('photo_page' => 1);
        $profile['base_url']   = href_to_profile($profile, array('content', $ctype['name']));

        $profile['user_id'] = $profile['id']; // для проверки прав доступа

        return $this->renderPhotosList($profile, 'user_id', $this->cms_core->request->get('photo_page', 1));

    }

}
