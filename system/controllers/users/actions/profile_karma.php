<?php
/**
 * @property \modelContent $model_content
 */
class actionUsersProfileKarma extends cmsAction {

    use icms\traits\services\fieldsParseable;

    public $lock_explicit_call = true;

    public function run($profile) {

        $tabs = $this->getProfileMenu($profile);

        if (!isset($this->tabs['karma'])) {
            return cmsCore::error404();
        }

        $page    = $this->request->get('page', 1);
        $perpage = 15;

        $total = $this->model->getKarmaLogCount($profile['id']);
        $log   = $this->model->limitPage($page, $perpage)->getKarmaLog($profile['id']);

        $fields = $this->parseContentFields(
            $this->model_content->setTablePrefix('')->getContentFields('{users}'),
            $profile
        );

        $meta_profile = $this->prepareItemSeo($profile, $fields, ['name' => 'users']);

        $meta_profile['tab_title'] = $this->tabs['karma']['title'];

        $this->cms_template->render('profile_karma', [
            'user'         => $this->cms_user,
            'meta_profile' => $meta_profile,
            'tabs'         => $tabs,
            'fields'       => $fields,
            'tab'          => $this->tabs['karma'],
            'profile'      => $profile,
            'log'          => $log,
            'total'        => $total,
            'page'         => $page,
            'perpage'      => $perpage
        ]);
    }

}
