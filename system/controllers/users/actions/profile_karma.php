<?php

class actionUsersProfileKarma extends cmsAction {

    public $lock_explicit_call = true;

    public function run($profile){

        $tabs = $this->controller->getProfileMenu($profile);

        if (!isset($this->tabs['karma'])){
            cmsCore::error404();
        }

        $page = $this->request->get('page', 1);
        $perpage = 15;

        $total = $this->model->getKarmaLogCount($profile['id']);
        $log   = $this->model->limitPage($page, $perpage)->getKarmaLog($profile['id']);

        $this->cms_template->render('profile_karma', array(
            'user'    => $this->cms_user,
            'tabs'    => $tabs,
            'tab'     => $this->tabs['karma'],
            'profile' => $profile,
            'log'     => $log,
            'total'   => $total,
            'page'    => $page,
            'perpage' => $perpage
        ));

    }

}
