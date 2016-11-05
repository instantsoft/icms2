<?php

class wall extends cmsFrontend {

    public static $perpage = 15;

    public function getWidget($title, $target, $permissions=array()){

        extract($target);

        $page          = $this->request->get('page', 1);
        $show_id       = $this->request->get('wid', 0);
        $go_reply      = $this->request->get('reply', 0);
        $show_reply_id = 0;

        if ($show_id){

            $entry = $this->model->getEntry($show_id);

            if ($entry){

                if ($entry['parent_id'] > 0) {
                    $show_id       = $entry['parent_id'];
                    $show_reply_id = $entry['id'];
                }

                $page = $this->model->getEntryPageNumber($show_id, $target, self::$perpage);

            }

        }

        $total   = $this->model->getEntriesCount($profile_type, $profile_id);
        $entries = $this->model->getEntries($profile_type, $profile_id, $page);

        $entries = cmsEventsManager::hook('wall_before_list', $entries);

        $csrf_token_seed = implode('/', array($profile_type, $profile_id));

        return $this->cms_template->renderInternal($this, 'list', array(
            'title'           => $title,
            'controller'      => $controller,
            'profile_type'    => $profile_type,
            'profile_id'      => $profile_id,
            'user'            => $this->cms_user,
            'entries'         => $entries,
            'permissions'     => $permissions,
            'page'            => $page,
            'perpage'         => wall::$perpage,
            'total'           => $total,
            'max_entries'     => $show_id ? 0 : 5,
            'csrf_token_seed' => $csrf_token_seed,
            'show_id'         => $show_id,
            'show_reply_id'   => $show_reply_id,
            'go_reply'        => $go_reply
        ));

    }

}
