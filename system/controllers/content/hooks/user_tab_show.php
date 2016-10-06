<?php

class onContentUserTabShow extends cmsAction {

    public function run($profile, $ctype_name){

        $ctype = $this->model->getContentTypeByName($ctype_name);
        if (!$ctype) { cmsCore::error404(); }

        $this->model->filterEqual('user_id', $profile['id']);

        $page_url = href_to('users', $profile['id'], $ctype_name);

        if ($this->cms_user->id != $profile['id'] && !$this->cms_user->is_admin){
            $this->model->filterHiddenParents();
        }

        if ($this->cms_user->id == $profile['id'] || $this->cms_user->is_admin){
            $this->model->disableApprovedFilter();
        }

        list($ctype, $profile) = cmsEventsManager::hook('content_before_profile', array($ctype, $profile));

        $list_html = $this->renderItemsList($ctype, $page_url);

        return $this->cms_template->renderInternal($this, 'profile_tab', array(
            'user'    => $this->cms_user,
            'profile' => $profile,
            'ctype'   => $ctype,
            'html'    => $list_html
        ));

    }

}
