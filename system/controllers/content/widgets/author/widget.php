<?php
/**
 * Виджет выводит автора, если мы на странице записи или в списке личных записей
 */
class widgetContentAuthor extends cmsWidget {

    use icms\traits\services\fieldsParseable;

    public $is_cacheable = false;

    public function run() {

        $ctype = cmsModel::getCachedResult('current_ctype');

        if (!$ctype) {
            return false;
        }

        // Поля для вывода
        $show_fields = array_filter($this->getOption('show_fields', []));

        if (!$show_fields) {
            return false;
        }

        // Флаг, что мы в записи
        $is_item_page = false;

        // Если мы в записи
        $item = cmsModel::getCachedResult('current_ctype_item');

        // Если мы в списке личных записей
        $profile = cmsModel::getCachedResult('current_user_profile');

        if (!$item && !$profile) {
            return false;
        }

        // Виджет в записи
        if(!$profile){

            $is_item_page = true;

            $profile = $this->model_users->getUser($item['user_id']);
            if(!$profile){
                return false;
            }
        }

        $fields = $this->model_content->setTablePrefix('')->getContentFields('{users}', false, false, $show_fields);

        $fields = $this->parseContentFields($fields, $profile);

        // Строим поля, которые выведем в шаблоне
        $profile['fields'] = $this->getViewableItemFields($fields, $profile, 'id');

        // Применяем хуки полей к записи
        $profile = $this->applyFieldHooksToItem($profile['fields'], $profile);

        $fieldsets = cmsForm::mapFieldsToFieldsets($profile['fields'], function($field, $user) {
            return empty($field['is_system']);
        });

        // Микроразметка
        $jsonld = [];
        if ($is_item_page && $this->getOption('generate_schemaorg')){

            $jsonld = [
                '@context'       => 'https://schema.org',
                '@type'          => 'Article',
                'url'            => href_to_abs($ctype['name'], $item['slug'].'.html'),
                'headline'       => $item['title'],
                'articleSection' => !empty($item['category']['title']) ? $item['category']['title'] : '',
                'commentCount'   => $item['comments'],
                'discussionUrl'  => href_to_abs($ctype['name'], $item['slug'].'.html') . '#comments',
                'datePublished'  => date('Y-m-d', strtotime($item['date_pub'])),
                'dateModified'   => date('Y-m-d', strtotime($item['date_last_modified'])),
                'author'         => [
                    '@type'         => 'Person',
                    'name'          => $item['user_nickname'],
                    'image'         => html_avatar_image_src($profile['avatar'], $fields['avatar']['options']['size_full'], false),
                    'url'           => href_to_profile($profile, [], true)
                ]
            ];

            $schemaorg_addons = $this->getOption('schemaorg_addons');

            if($schemaorg_addons){

                $schemaorg_addons = json_decode($schemaorg_addons, true);

                if($schemaorg_addons){
                    $schemaorg_addons = $this->replaceSchemaorgValues([
                        'profile' => $profile,
                        'item' => $item
                    ], $schemaorg_addons);
                }

                $jsonld = array_replace_recursive($jsonld, $schemaorg_addons);
            }
        }

        return [
            'sys_fields' => $this->getSystemFields($profile, $ctype),
            'item'       => $item,
            'jsonld'     => $jsonld,
            'ctype'      => $ctype,
            'profile'    => $profile,
            'fields'     => $fields,
            'fieldsets'  => $fieldsets
        ];
    }

    private function getSystemFields($profile, $ctype) {

        $fields = [];

        if ($this->getOption('show_user_groups')){

            $groups_title = [];

            $groups = $this->model_users->getGroups();

            foreach ($profile['groups'] as $group_id) {
                $groups_title[] = $groups[$group_id]['title'];
            }

            $fields['groups'] = [
                'title' => LANG_GROUPS,
                'text'  => implode(', ', $groups_title)
            ];
        }

        if($this->getOption('show_date_reg')) {

            $fields['date_reg'] = [
                'title' => LANG_REGISTRATION,
                'text'  => string_date_age_max($profile['date_reg'], true)
            ];
        }

        if($this->getOption('show_date_log')) {
            $fields['date_log'] = [
                'title' => LANG_USERS_PROFILE_LOGDATE,
                'text'  => $profile['is_online'] ? LANG_ONLINE : string_date_age_max($profile['date_log'], true)
            ];
        }


        // Учитываем приватность
        if($this->getOption('show_user_items_link') && cmsUser::getInstance()->isPrivacyAllowed($profile, 'view_user_'.$ctype['name'])) {
            $fields['user_items_link'] = [
                'href'  => href_to_profile($profile, ['content', $ctype['name']]),
                'icon'  => 'book-reader',
                'text'  => $this->getOption('user_items_link_title') ?: sprintf(LANG_WD_CON_AUTHOR_READ, mb_strtolower($ctype['labels']['profile']))
            ];
        }

        $hook = cmsEventsManager::hook('widget_content_author_sys_fields', array(
            'profile' => $profile,
            'fields'  => $fields
        ));

        return $hook['fields'];
    }

    private function replaceSchemaorgValues($data, $schemaorg_addons) {

        foreach ($schemaorg_addons as $key => $value) {

            if(is_array($value)){
                $schemaorg_addons[$key] = $this->replaceSchemaorgValues($data, $value);
            } else {
                $schemaorg_addons[$key] = string_replace_keys_values_extended($value, $data);
            }

        }

        return $schemaorg_addons;
    }

}
