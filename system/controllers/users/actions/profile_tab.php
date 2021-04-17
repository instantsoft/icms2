<?php

class actionUsersProfileTab extends cmsAction {

    public $lock_explicit_call = true;

    public function run($profile, $tab_name, $dataset = false) {

        // Доступность профиля для данного пользователя
        if (!$this->cms_user->isPrivacyAllowed($profile, 'users_profile_view')) {
            cmsCore::error404();
        }

        $tabs_menu = $this->getProfileMenu($profile);

        if (!isset($this->tabs[$tab_name]) || !$this->tabs[$tab_name]) {
            cmsCore::error404();
        }

        $tab = $this->tabs[$tab_name];

        if (!isset($this->tabs_controllers[$tab['controller']])) {
            cmsCore::error404();
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

        $html = $controller->runHook('user_tab_show', array($profile, $tab_name, $tab));
        if (!$html) { cmsCore::error404(); }

        // Получаем поля
        $fields = $this->model_content->setTablePrefix('')->orderBy('ordering')->getContentFields('{users}');

        // Парсим значения полей
        foreach ($fields as $name => $field) {
            $fields[$name]['string_value'] = $field['handler']->setItem($profile)->getStringValue($profile[$name]);
        }

        $meta_profile = $this->prepareItemSeo($profile, $fields, ['name' => 'users']);

        $this->cms_template->addHead('<link rel="canonical" href="' . href_to_profile($profile, [$tab_name], true) . '"/>');

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
