<?php

class onContentUserTabShow extends cmsAction {

    public function run($profile, $ctype_name){

        $user = cmsUser::getInstance();
        $template = cmsTemplate::getInstance();

        $ctype = $this->model->getContentTypeByName($ctype_name);
        if (!$ctype) { cmsCore::error404(); }

        $this->model->filterEqual('user_id', $profile['id']);

        $page_url = href_to('users', $profile['id'], $ctype_name);

        if ($user->id != $profile['id'] && !$user->is_admin){
            $this->model->filterHiddenParents();
        }

        if ($user->id == $profile['id'] || $user->is_admin){
            $this->model->disableApprovedFilter();            
        }

        cmsEventsManager::hook("content_before_profile", array($ctype, $profile));

        $list_html = $this->renderItemsList($ctype, $page_url);

        return $template->renderInternal($this, 'profile_tab', array(
            'user' => $user,
            'profile' => $profile,
            'ctype' => $ctype,
            'html' => $list_html
        ));

    }

}
