<?php

class onWallProcessRenderGroupsGroupView extends cmsAction {

    public function run($_data) {

        list($tpl_file, $data, $request) = $_data;

        if (empty($data['options']['is_wall'])) {
            return $_data;
        }

        $this->setRequest($request);

        $wall_html = $this->getWidget(LANG_GROUPS_WALL, [
            'controller'   => 'groups',
            'profile_type' => 'group',
            'profile_id'   => $data['group']['id']
        ], $data['group']['access']['wall']);

        $this->cms_template->addToBlock('groups_group_view_bottom', $wall_html);

        return $_data;
    }

}
