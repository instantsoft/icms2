<?php
/**
 * @property \modelContent $model_content
 */
class actionUsersProfileTab extends cmsAction {

    use icms\traits\services\fieldsParseable;

    public $lock_explicit_call = true;

    public function run($profile, $tab_name, $dataset = false) {

        $tabs_menu = $this->getProfileMenu($profile);

        if (empty($this->tabs[$tab_name])) {
            return cmsCore::error404();
        }

        $tab = $this->tabs[$tab_name];

        if (!isset($this->tabs_controllers[$tab['controller']])) {
            return cmsCore::error404();
        }

        $controller = $this->tabs_controllers[$tab['controller']];

        unset($this->tabs);
        unset($this->tabs_controllers);

        $this->request->set('dataset', $dataset);

        $this->cms_template->setPageTitle($tab['title'], $profile['nickname']);

        if ($this->listIsAllowed()) {
            $this->cms_template->addBreadcrumb(LANG_USERS, href_to('users'));
        }
        $this->cms_template->addBreadcrumb($profile['nickname'], href_to_profile($profile));

        $html = $controller->runHook('user_tab_show', [$profile, $tab_name, $tab]);
        if (!$html) {
            return cmsCore::error404();
        }

        $fields = $this->parseContentFields(
            $this->model_content->setTablePrefix('')->getContentFields('{users}'),
            $profile
        );

        $meta_profile = $this->prepareItemSeo($profile, $fields, ['name' => 'users']);

        $meta_profile['tab_title'] = $tab['title'];

        $this->cms_template->addHead('<link rel="canonical" href="' . href_to_profile($profile, [$tab_name], true) . '">');

        $this->cms_template->render('profile_tab', [
            'tabs'         => $tabs_menu,
            'meta_profile' => $meta_profile,
            'fields'       => $fields,
            'tab'          => $tab,
            'profile'      => $profile,
            'user'         => $this->cms_user,
            'html'         => $html
        ]);
    }

}
