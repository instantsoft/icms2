<?php

class widgetContentList extends cmsWidget {

    public function run() {

        $ctype_id        = $this->getOption('ctype_id');
        $dataset_id      = $this->getOption('dataset');
        $relation_id     = $this->getOption('relation_id');
        $filter_id       = $this->getOption('filter_id');
        $filter_hook     = $this->getOption('filter_hook');
        $cat_id          = $this->getOption('category_id');
        $image_field     = $this->getOption('image_field');
        $teaser_field    = $this->getOption('teaser_field');
        $is_show_details = $this->getOption('show_details');
        $limit           = $this->getOption('limit', 10);
        $teaser_len      = $this->getOption('teaser_len', 100);

        $current_ctype_item = cmsModel::getCachedResult('current_ctype_item');
        $current_ctype      = cmsModel::getCachedResult('current_ctype');

        $model = cmsCore::getModel('content');

        if ($ctype_id) {
            $ctype = $model->getContentType($ctype_id);
        } else {
            $ctype = $current_ctype;
        }

        if (!$ctype) {
            return false;
        }

        // Получаем поля
        $fields = $model->getContentFields($ctype['name']);

        // Включенные поля для показа
        $show_fields  = $this->getOption('show_fields', []);
        $shown_fields = [];
        if (isset($show_fields[$ctype['id']])) {
            foreach ($show_fields[$ctype['id']] as $field_name => $is_enabled) {
                if ($is_enabled) {
                    $shown_fields[] = $field_name;
                }
            }
        }
        if (!$shown_fields) {
            $shown_fields = ['title', 'content'];
        }
        // Опции полей
        $show_fields_options  = $this->getOption('show_fields_options', []);
        $shown_fields_options = [];
        if (isset($show_fields_options[$ctype['id']])) {
            $shown_fields_options = $show_fields_options[$ctype['id']];
        }

        // Сортировка полей, если задана
        foreach ($fields as $name => $field) {
            if (!empty($shown_fields_options[$name]['ordering'])) {
                $fields[$name]['ordering'] = $shown_fields_options[$name]['ordering'];
            }
        }
        array_order_by($fields, 'ordering');

        // Получаем категорию, если задана
        if ($cat_id) {
            $category = $model->getCategory($ctype['name'], $cat_id);
        } else {
            $category = false;
        }

        // Набор
        if ($dataset_id) {
            $dataset = $model->getContentDataset($dataset_id);
        }

        // Фильтр
        if ($filter_id) {
            $filter = $model->getContentFilter($ctype, $filter_id);
        }

        // Связь
        if ($relation_id && $current_ctype_item && $current_ctype) {

            $parents = $model->getContentTypeParents($ctype_id);

            if ($parents) {
                foreach ($parents as $parent) {
                    if ($parent['id'] == $relation_id) {

                        $filter = "r.parent_ctype_id = {$current_ctype['id']} AND " .
                                "r.parent_item_id = {$current_ctype_item['id']} AND " .
                                "r.child_ctype_id = {$ctype_id} AND " .
                                "r.child_item_id = i.id";

                        $this->disableCache();

                        $model->joinInner('content_relations_bind', 'r', $filter);

                        $this->title = string_replace_keys_values($this->title, $current_ctype_item);

                        $this->links = str_replace('{list_link}', href_to($current_ctype['name'], $current_ctype_item['slug'], "view-{$ctype['name']}"), $this->links);

                        break;
                    }
                }
            }
        }

        // Применяем фильтры, если есть
        if (!empty($filter['filters'])) {
            foreach ($filter['filters'] as $fname => $fvalue) {
                if (isset($fields[$fname])) {
                    $fields[$fname]['handler']->applyFilter($model, $fvalue);
                }
            }
        }

        // Применяем набор
        if (!empty($dataset)) {
            $model->applyDatasetFilters($dataset);
        }

        // Фильтр по категории
        if ($category) {
            $model->filterCategory($ctype['name'], $category, true, !empty($ctype['options']['is_cats_multi']));
        }

        // применяем приватность
        // флаг показа только названий
        $hide_except_title = $model->applyPrivacyFilter($ctype, cmsUser::isAllowed($ctype['name'], 'view_all'));

        // Скрываем записи из скрытых родителей (приватных групп и т.п.)
        $model->enableHiddenParentsFilter();

        if ($this->getOption('widget_type') === 'related') {

            if ($current_ctype_item) {

                $this->disableCache();

                $model->filterRelated('title', $current_ctype_item['title']);

                if ($current_ctype_item['ctype_name'] == $ctype['name']) {
                    $model->filterNotEqual('id', $current_ctype_item['id']);
                }
            } else {
                return false;
            }
        }

        // мы на странице группы?
        $current_group = cmsModel::getCachedResult('current_group');
        if ($this->getOption('auto_group') && $current_group) {

            $this->disableCache();

            $model->filterEqual('parent_id', $current_group['id'])->
                    filterEqual('parent_type', 'group');
        }

        // мы на странице записи
        if ($this->getOption('auto_user') && !empty($current_ctype_item['user_id'])) {

            $this->disableCache();

            $model->filterEqual('user_id', $current_ctype_item['user_id']);
            $model->filterNotEqual('id', $current_ctype_item['id']);
        }

        // выключаем формирование рейтинга в хуках
        $ctype['is_rating'] = 0;

        list($ctype, $model) = cmsEventsManager::hook("content_list_filter", [$ctype, $model]);
        list($ctype, $model) = cmsEventsManager::hook("content_{$ctype['name']}_list_filter", [$ctype, $model]);

        if ($filter_hook) {
            list($ctype, $model) = cmsEventsManager::hook($filter_hook, [$ctype, $model]);
        }

        $items = $model->
                limit($limit)->
                getContentItems($ctype['name']);
        if (!$items) {
            return false;
        }

        $user = cmsUser::getInstance();

        if ($items) {
            foreach ($items as $key => $item) {

                $item['ctype']             = $ctype;
                $item['ctype_name']        = $ctype['name'];
                $item['is_private_item']   = $item['is_private'] && $hide_except_title;
                $item['private_item_hint'] = LANG_PRIVACY_HINT;
                $item['fields']            = [];

                // для приватности друзей
                // другие проверки приватности (например для групп) в хуках content_before_list
                if ($item['is_private'] == 1) {
                    $item['is_private_item']   = $item['is_private_item'] && !$item['user']['is_friend'];
                    $item['private_item_hint'] = LANG_PRIVACY_PRIVATE_HINT;
                }

                // Флаг, что эту запись пользователь не видел с последнего визита
                $item['is_new'] = (strtotime($item['date_pub']) > strtotime($user->date_log));

                // строим поля списка
                foreach ($fields as $field) {

                    if ($field['is_system']) {
                        continue;
                    }
                    // Только включенные поля
                    if (!in_array($field['name'], $shown_fields)) {
                        continue;
                    }

                    // проверяем что группа пользователя имеет доступ к чтению этого поля
                    if ($field['groups_read'] && !$user->isInGroups($field['groups_read'])) {
                        // если группа пользователя не имеет доступ к чтению этого поля,
                        // проверяем на доступ к нему для авторов
                        if (empty($item['user_id']) || empty($field['options']['author_access'])) {
                            continue;
                        }
                        if (!in_array('is_read', $field['options']['author_access'])) {
                            continue;
                        }
                        if ($item['user_id'] != $user->id) {
                            continue;
                        }
                    }

                    if (!isset($shown_fields_options[$field['name']]['label_in_list'])) {
                        $label_pos = 'none';
                    } else {
                        $label_pos = $shown_fields_options[$field['name']]['label_in_list'];
                    }

                    $current_field_data = [
                        'label_pos' => $label_pos,
                        'type'      => $field['type'],
                        'name'      => $field['name'],
                        'title'     => $field['title']
                    ];

                    if (!array_key_exists($field['name'], $item)) {

                        // Виртуальное поле. В таблице ячейки может не быть.
                        if($field['handler']->is_virtual){
                            $item[$field['name']] = '';
                        } else {
                            continue;
                        }
                    }

                    // Меняем опции поля, если есть
                    if (!empty($shown_fields_options[$field['name']])) {
                        foreach ($shown_fields_options[$field['name']] as $opt_name => $opt_value) {
                            $field['handler']->setOption($opt_name, $opt_value);
                            $field['options'][$opt_name] = $opt_value;
                        }
                    }

                    $field_html = $field['handler']->setItem($item)->parseTeaser($item[$field['name']]);
                    if (is_empty_value($field_html)) {
                        continue;
                    }

                    $current_field_data['html']    = $field_html;
                    $current_field_data['options'] = $field['options'];

                    $item['fields'][$field['name']] = $current_field_data;
                }

                $item['info_bar'] = $this->getItemInfoBar($ctype, $item, $fields, $shown_fields);

                $items[$key] = $item;
            }
        }

        if (!in_array('comments', $shown_fields)) {
            $ctype['is_comments'] = 0;
        }

        list($ctype, $items) = cmsEventsManager::hook('content_before_list', [$ctype, $items]);
        list($ctype, $items) = cmsEventsManager::hook("content_{$ctype['name']}_before_list", [$ctype, $items]);

        return [
            'fields'            => $fields,
            'ctype'             => $ctype,
            'hide_except_title' => $hide_except_title,
            'teaser_len'        => $teaser_len,
            'image_field'       => $image_field,
            'teaser_field'      => $teaser_field,
            'is_show_details'   => $is_show_details,
            'items'             => $items
        ];
    }

    private function getItemInfoBar($ctype, $item, $fields, $shown_fields) {

        $bar  = [];

        $user = cmsUser::getInstance();

        if (!empty($fields['date_pub']) && in_array('date_pub', $shown_fields) && $user->isInGroups($fields['date_pub']['groups_read'])) {
            $bar['date_pub'] = [
                'css'   => 'bi_date_pub',
                'icon'  => 'calendar-alt',
                'html'  => isset($fields['date_pub']['html']) ? $fields['date_pub']['html'] : $fields['date_pub']['handler']->parse($item['date_pub']),
                'title' => $fields['date_pub']['title']
            ];
        }

        if (!empty($fields['user']) && in_array('user', $shown_fields) && $user->isInGroups($fields['user']['groups_read'])) {
            $bar['user'] = [
                'css'    => 'bi_user',
                'icon'   => 'user',
                'avatar' => isset($item['user']['avatar']) ? $item['user']['avatar'] : [],
                'href'   => href_to_profile($item['user']),
                'html'   => $item['user']['nickname']
            ];
        }

        return $bar;
    }

}
