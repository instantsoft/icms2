<?php

class onWallProcessRenderUsersProfileView extends cmsAction {

    public function run($_data) {

        list($tpl_file, $data, $request) = $_data;

        if (empty($data['options']['is_wall'])) {
            return $_data;
        }

        $this->setRequest($request);

        $wall_target = [
            'controller'   => 'users',
            'profile_type' => 'user',
            'profile_id'   => $data['profile']['id']
        ];

        $wall_permissions = cmsCore::getController('users', $request)->runHook('wall_permissions', [
            'profile_type' => 'user',
            'profile_id'   => $data['profile']
        ]);

        $wall_html = $this->getWidget(LANG_USERS_PROFILE_WALL, $wall_target, $wall_permissions);

        $this->cms_template->addToBlock('users_profile_view_bottom', $wall_html);

        return $_data;
    }

}
