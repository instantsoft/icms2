<?php

class actionGroupsGroup extends cmsAction {

    public $lock_explicit_call = true;

    public function run($group){

        // Стена
        if ($this->options['is_wall']){

            $wall_html = cmsCore::getController('wall', $this->request)->getWidget(LANG_GROUPS_WALL, array(
                'controller'   => 'groups',
                'profile_type' => 'group',
                'profile_id'   => $group['id']
            ), $group['access']['wall']);

        }

        $fields = array();

        // Парсим значения полей
        foreach($group['fields'] as $name => $field){
            $fields[$name] = $field;
            $fields[$name]['html'] = $field['handler']->setItem($group)->parse($group[$name]);
        }

        list($group, $fields) = cmsEventsManager::hook('group_before_view', array($group, $fields));

        $group['fields'] = $fields;

        $fields_fieldsets = cmsForm::mapFieldsToFieldsets($group['fields'], function($field, $user) use ($group) {
            if (!$field['is_in_item'] || $field['is_system']) { return false; }
            if ((empty($group[$field['name']]) || empty($field['html'])) && $group[$field['name']] !== '0') { return false; }
            // проверяем что группа пользователя имеет доступ к чтению этого поля
            if ($field['groups_read'] && !$user->isInGroups($field['groups_read'])) {
                // если группа пользователя не имеет доступ к чтению этого поля,
                // проверяем на доступ к нему для авторов
                if (!empty($group['owner_id']) && !empty($field['options']['author_access'])){
                    if (!in_array('is_read', $field['options']['author_access'])){ return false; }
                    if ($group['owner_id'] == $user->id){ return true; }
                }
                return false;
            }
            return true;
        });

        // Проверяем прохождение модерации
        if (!$group['is_approved']){
            if (!$group['access']['is_moderator'] && !$group['access']['is_owner']){ return cmsCore::errorForbidden(LANG_MODERATION_NOTICE, true); }

            $item_view_notice = LANG_MODERATION_NOTICE;

            if($group['access']['is_moderator']){
                $item_view_notice = LANG_MODERATION_NOTICE_MODER;
            }

            cmsUser::addSessionMessage($item_view_notice, 'info');
        }

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
