<?php

class actionGroupsGroup extends cmsAction {

    public $lock_explicit_call = true;

    public function run($group){

        // Стена
        if ($this->options['is_wall']){

            $wall_controller = cmsCore::getController('wall', $this->request);

            $wall_title = LANG_GROUPS_WALL;

            $wall_target = array(
                'controller'   => 'groups',
                'profile_type' => 'group',
                'profile_id'   => $group['id']
            );

            $wall_permissions = array(

                'add' =>$this->cms_user->is_admin || (
                            $group['access']['is_member'] && (
                                ($group['wall_policy'] == groups::WALL_POLICY_MEMBERS) ||
                                ($group['wall_policy'] == groups::WALL_POLICY_STAFF && $group['access']['member_role']==groups::ROLE_STAFF) ||
                                $group['access']['is_owner']
                            )
                        ),

                'delete' => ($this->cms_user->is_admin || $group['access']['is_owner']),

            );

            $wall_html = $wall_controller->getWidget($wall_title, $wall_target, $wall_permissions);

        }

        // Парсим значения полей
        foreach($group['fields'] as $name => $field){
            $group['fields'][$name]['html'] = $field['handler']->setItem($group)->parse($group[$name]);
        }

        list($group, $group['fields']) = cmsEventsManager::hook('group_before_view', array($group, $group['fields']));

        $fields_fieldsets = cmsForm::mapFieldsToFieldsets($group['fields'], function($field, $user) use ($group) {
            if (!$field['is_in_item'] || $field['is_system']) { return false; }
            if ((empty($group[$field['name']]) || empty($field['html'])) && $group[$field['name']] !== '0') { return false; }
            if ($field['groups_read'] && !$user->isInGroups($field['groups_read'])) { return false; }
            return true;
        });

        $this->cms_template->setPageTitle($group['title']);
        $this->cms_template->setPageDescription($group['description'] ? string_get_meta_description($group['description']): $group['title']);

        $this->cms_template->addBreadcrumb(LANG_GROUPS, href_to('groups'));
        $this->cms_template->addBreadcrumb($group['title']);

        return $this->cms_template->render('group_view', array(
            'group'            => $group,
            'fields_fieldsets' => $fields_fieldsets,
            'user'             => $this->cms_user,
            'wall_html'        => isset($wall_html) ? $wall_html : false
        ));

    }

}
