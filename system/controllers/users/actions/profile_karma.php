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

        $fields = $this->model_content->setTablePrefix('')->orderBy('ordering')->getContentFields('{users}');

        $meta_profile = $this->prepareItemSeo($profile, $fields, ['name' => 'users']);

        $this->cms_template->render('profile_karma', array(
            'user'    => $this->cms_user,
            'meta_profile' => $meta_profile,
            'tabs'    => $tabs,
            'fields'  => $fields,
            'tab'     => $this->tabs['karma'],
            'profile' => $profile,
            'log'     => $log,
            'total'   => $total,
            'page'    => $page,
            'perpage' => $perpage
        ));

    }

}
