<?php

class actionGroupsGroup extends cmsAction {

    use icms\traits\services\fieldsParseable;

    public $lock_explicit_call = true;

    public function run($group, $display_closed = false) {

        // Получаем поля для данного типа контента
        // И парсим их, получая HTML полей
        $group['fields'] = $this->parseContentFields($group['fields'], $group);

        list($group, $fields) = cmsEventsManager::hook('group_before_view', [$group, $group['fields']]);

        // Строим поля, которые выведем в шаблоне
        $group['fields'] = $this->getViewableItemFields($fields, $group, 'owner_id', function($field, $item) use($display_closed) {
            return !($display_closed && !$field['is_in_closed']);
        });

        // Применяем хуки полей к записи
        $group = $this->applyFieldHooksToItem($group['fields'], $group);

        $fields_fieldsets = cmsForm::mapFieldsToFieldsets($group['fields'], function ($field, $user) {
            return empty($field['is_system']);
        });

        // Проверяем прохождение модерации
        if (!$group['is_approved']) {

            if (!$group['access']['is_moderator'] && !$group['access']['is_owner']) {
                return cmsCore::errorForbidden(LANG_MODERATION_NOTICE, true);
            }

            $item_view_notice = LANG_MODERATION_NOTICE;

            if ($group['access']['is_moderator']) {
                $item_view_notice = LANG_MODERATION_NOTICE_MODER;
            }

            cmsUser::addSessionMessage($item_view_notice, 'info');
        }

        $this->cms_template->addBreadcrumb(LANG_GROUPS, href_to('groups'));
        $this->cms_template->addBreadcrumb($group['title']);

        return $this->cms_template->render('group_view', [
            'display_closed'   => $display_closed,
            'options'          => $this->options,
            'group'            => $group,
            'fields_fieldsets' => $fields_fieldsets,
            'user'             => $this->cms_user,
            'wall_html'        => false // Не используется, чтобы нотиса в старых шаблонах не было
        ]);
    }

}
