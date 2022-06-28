<?php
/**
 * Виджет выводит автора, если мы на странице записи или в списке личных записей
 */
class widgetContentAuthor extends cmsWidget {

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
        $fields = cmsModel::getCachedResult('current_user_fields');

        if (!$item && !$profile) {
            return false;
        }

        // Виджет в записи
        if(!$profile){

            $is_item_page = true;

            $profile = cmsCore::getModel('users')->getUser($item['user_id']);
            if(!$profile){
                return false;
            }

            $fields = cmsCore::getModel('content')->setTablePrefix('')->getContentFields('{users}', false, false);
        }

        foreach($fields as $name => $field){

            if(!in_array($name, $show_fields)){
                continue;
            }

            $fields[$name]['html'] = $field['handler']->setItem($profile)->parse($profile[$name]);
        }

        $fieldsets = cmsForm::mapFieldsToFieldsets($fields, function($field, $user) use ($profile){

            if ($field['is_system'] || empty($profile[$field['name']])) { return false; }

            // проверяем что группа пользователя имеет доступ к чтению этого поля
            if ($field['groups_read'] && !$user->isInGroups($field['groups_read'])) {
                // если группа пользователя не имеет доступ к чтению этого поля,
                // проверяем на доступ к нему для авторов
                if (!empty($field['options']['author_access'])){
                    if (!in_array('is_read', $field['options']['author_access'])){ return false; }
                    if ($profile['id'] == $user->id){ return true; }
                }
                return false;
            }
            return true;

        }, $profile);

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

            $groups = cmsCore::getModel('users')->getGroups();

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
