<?php

class actionAdminUsersFilter extends cmsAction {

    public function run() {

        $fields = cmsCore::getModel('content')->setTablePrefix('')->getContentFields('{users}');

        $fields[] = [
            'title'   => LANG_RATING,
            'name'    => 'rating',
            'handler' => new fieldNumber('rating')
        ];

        $fields[] = [
            'title'   => LANG_KARMA,
            'name'    => 'karma',
            'handler' => new fieldNumber('karma')
        ];

        $fields[] = [
            'title'   => LANG_USER_IS_ADMIN,
            'name'    => 'is_admin',
            'handler' => new fieldCheckbox('is_admin')
        ];

        $fields = cmsEventsManager::hook('admin_users_filter', $fields);

        return $this->cms_template->render('grid_advanced_filter', [
            'fields' => $fields
        ]);
    }

}
