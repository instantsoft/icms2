<?php

class actionContentTrash extends cmsAction {

    public function run($ctype_name = null){

        $_ctypes = $this->model->getContentTypes();
        if (!$_ctypes) { cmsCore::error404(); }

        $ctypes = array();

        foreach($_ctypes as $ctype){
            if (!cmsUser::isAllowed($ctype['name'], 'restore')) { continue; }
            $ctypes[$ctype['name']] = $ctype;
        }

        if(!$ctypes){ cmsCore::error404(); }

        $counts = $this->getDeletedCounts($ctypes);

        foreach($counts as $_ctype_name => $count){
            if(!$count){ unset($ctypes[$_ctype_name]); }
        }

        if(empty($ctypes)){
            return $this->cms_template->render('trash_empty', array(), $this->request);
        }

        $is_index = false;

        $ctypes_list = array_keys($ctypes);

        if (!$ctype_name) {

            $ctype_name = $ctypes_list[0]; $is_index = true;

        } else {

            if(!isset($ctypes[$ctype_name])){ cmsCore::error404(); }

        }

        $page_url = $is_index ? href_to($this->name, 'trash') : href_to($this->name, 'trash', $ctype_name);

        $ctype = $ctypes[$ctype_name];

		$list_html = $this->filterDeleted($ctype)->setListContext('trash')->renderItemsList($ctype, $page_url, true);

        return $this->cms_template->render('trash', array(
            'is_index'   => $is_index,
            'counts'     => $counts,
            'ctype'      => $ctype,
            'ctypes'     => $ctypes,
            'ctype_name' => $ctype_name,
            'list_html'  => $list_html
        ), $this->request);

    }

    private function getDeletedCounts($ctypes){

        $counts = array();

        foreach($ctypes as $ctype){

            $this->filterDeleted($ctype);
                $counts[$ctype['name']] = $this->model->getCount($this->model->table_prefix . $ctype['name']);
            $this->model->resetFilters();
        }

        return $counts;

    }

    private function filterDeleted($ctype){

        $is_moderator = $this->controller_moderation->userIsContentModerator($ctype['name'], $this->cms_user->id);

        if($is_moderator){
            $this->model->disableApprovedFilter()->disablePubFilter()->disablePrivacyFilter();
        }

        $this->model->disableDeleteFilter()->filterDeleteOnly();

        if(cmsUser::isAllowed($ctype['name'], 'restore', 'own') && !cmsUser::isAllowed($ctype['name'], 'restore', 'all')){

            $this->model->filterEqual('user_id', $this->cms_user->id);

            $this->model->disableApprovedFilter();
			$this->model->disablePubFilter();
			$this->model->disablePrivacyFilter();

        }

        return $this;
    }

}
