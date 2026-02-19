<?php
/**
 * @property \modelContent $model_content
 */
class widgetContentList extends cmsWidget {

    /**
     * Context arrays
     * @var ?array
     */
    private $current_item;
    private $current_ctype;
    private $profile;
    private $current_group;
    /**
     * Флаг показа только названий
     * @var bool
     */
    private $hide_except_title = true;
    private $ctype = [];
    private $fields = [];
    private $visible_fields = [];

    public function run() {

        $this->loadContext();

        $model = $this->prepareModel();

        if (!$model) {
            return false;
        }

        $this->applyFilters($model);

        $items = $this->fetchItems($model);

        if (!$items) {
            return false;
        }

        $this->processTitle();

        return [
            'fields'            => $this->fields,
            'current_item'      => $this->current_item,
            'ctype'             => $this->ctype,
            'hide_except_title' => $this->hide_except_title,
            'teaser_len'        => $this->getOption('teaser_len', 100),
            'image_field'       => $this->getOption('image_field'),
            'teaser_field'      => $this->getOption('teaser_field'),
            'is_show_details'   => $this->getOption('show_details'),
            'items'             => $items
        ];
    }

    private function prepareModel() {

        $ctype_id = $this->getOption('ctype_id');

        $model = cmsCore::getModel('content');

        $ctype = $ctype_id ?
                 $model->getContentType($ctype_id) :
                 $this->current_ctype;

        if (!$ctype) { return false; }

        $this->ctype = $ctype;
        $this->fields = $model->getContentFields($ctype['name']);
        $this->loadVisibleFields();

        return $model;
    }

    private function loadVisibleFields() {

        // Включенные поля для показа
        $fields = $this->getOption('show_fields', []);
        // Опции полей
        $options = $this->getOption('show_fields_options', []);
        $field_options = $options[$this->ctype['id']] ?? [];

        $visible = [];
        if (isset($fields[$this->ctype['id']])) {
            $visible = array_keys(array_filter($fields[$this->ctype['id']]));
        }
        if (!$visible) {
            $visible = ['title'];
        }

        // Применить опции к полям
        foreach ($this->fields as &$field) {

            $over_opts = $field_options[$field['name']] ?? [];

            foreach ($over_opts as $opt_name => $opt_value) {
                $field['handler']->setOption($opt_name, $opt_value);
                $field['options'][$opt_name] = $opt_value;
            }

            if (!empty($over_opts['ordering'])) {
                $field['ordering'] = $over_opts['ordering'];
            }
        }

        array_order_by($this->fields, 'ordering');

        $this->visible_fields = $visible;
    }

    private function fetchItems($model) {

        $items = $model->limit($this->getOption('offset', 0), $this->getOption('limit', 10))->getContentItems($this->ctype['name']);

        if (!$items) {
            return false;
        }

        foreach ($items as &$item) {

            $item['ctype']             = $this->ctype;
            $item['ctype_name']        = $this->ctype['name'];
            $item['is_private_item']   = $item['is_private'] && $this->hide_except_title;
            $item['private_item_hint'] = LANG_PRIVACY_HINT;
            // для приватности друзей
            // другие проверки приватности (например для групп) в хуках content_before_list
            if ($item['is_private'] == 1) {
                $item['is_private_item']   = $item['is_private_item'] && !$item['user']['is_friend'];
                $item['private_item_hint'] = LANG_PRIVACY_PRIVATE_HINT;
            }

            // Флаг, что эту запись пользователь не видел с последнего визита
            $item['is_new']   = strtotime($item['date_pub']) > strtotime($this->cms_user->date_log);
            // Сформированные значения полей
            $item['fields']   = $this->buildItemFields($item);
            // Инфобар
            $item['info_bar'] = $this->getItemInfoBar($item);
        }

        if (!in_array('comments', $this->visible_fields)) {
            $this->ctype['is_comments'] = 0;
        }

        list($this->ctype, $items) = cmsEventsManager::hook('content_before_list', [$this->ctype, $items]);
        list($this->ctype, $items) = cmsEventsManager::hook("content_{$this->ctype['name']}_before_list", [$this->ctype, $items]);

        return $items;
    }

    private function processTitle() {
        if ($this->current_item) {
            $this->title = string_replace_keys_values_extended(string_replace_svg_icons($this->title), $this->current_item);
        }
        if ($this->profile) {
            $this->title = string_replace_keys_values_extended(string_replace_svg_icons($this->title), $this->profile);
        }
    }

    private function loadContext() {
        $this->current_item  = cmsModel::getCachedResult('current_ctype_item');
        $this->current_ctype = cmsModel::getCachedResult('current_ctype');
        $this->profile       = cmsModel::getCachedResult('current_profile');
        $this->current_group = cmsModel::getCachedResult('current_group');
    }

    private function buildItemFields($item) {

        $output = [];

        foreach ($this->fields as $field) {
            // Только включенные поля и не системные поля
            if ($field['is_system'] || !in_array($field['name'], $this->visible_fields)) {
                continue;
            }
            // проверяем что группа пользователя имеет доступ к чтению этого поля
            if ($field['groups_read'] && !$this->cms_user->isInGroups($field['groups_read'])) {
                if (empty($item['user_id']) ||
                        !in_array('is_read', $field['options']['author_access'] ?? []) ||
                        $item['user_id'] != $this->cms_user->id) {
                    continue;
                }
            }

            $html = $field['handler']->setItem($item)->parseTeaser($item[$field['name']] ?? '');

            if (!is_empty_value($html)) {
                $output[$field['name']] = [
                    'label_pos' => $field['options']['label_in_list'] ?? 'none',
                    'type'      => $field['type'],
                    'name'      => $field['name'],
                    'title'     => $field['title'],
                    'html'      => $html,
                    'options'   => $field['options']
                ];
            }
        }

        return $output;
    }

    private function applyFilters($model) {

        $this->applyDataset($model);
        $this->applyCategory($model);
        $this->applyRelation($model);
        $this->applyWidgetType($model);
        $this->applyContentFilter($model);
        $this->applyGroupOrUser($model);
        $this->applyPrivacy($model);

        // выключаем формирование рейтинга в хуках
        $this->ctype['is_rating'] = 0;

        list($this->ctype, $model) = cmsEventsManager::hook('content_list_filter', [$this->ctype, $model]);
        list($this->ctype, $model) = cmsEventsManager::hook("content_{$this->ctype['name']}_list_filter", [$this->ctype, $model]);

        if (($filter_hook = $this->getOption('filter_hook'))) {
            list($this->ctype, $model) = cmsEventsManager::hook($filter_hook, [$this->ctype, $model]);
        }
    }

    private function applyWidgetType($model) {

        switch ($this->getOption('widget_type')) {
            case 'related':

                $this->applyRelatedFilter($model);
                break;

            case 'random':

                $this->applyRandomFilter($model);
                break;

            default:
                break;
        }
    }

    private function applyRelatedFilter($model) {

        if (!$this->current_item) {
            return;
        }

        $this->disableCache();

        if ($this->current_item['ctype_name'] === $this->ctype['name']) {
            $model->filterNotEqual('id', $this->current_item['id']);
        }

        switch ($this->getOption('related_type', 'title')) {
            case 'title':

                $match_fields = [];

                foreach ($this->fields as $f) {
                    // В настройках полей должно быть включено их участие в индексе
                    // Не проверяем права, поскольку будет ошибка в выборке по составному индексу
                    if ($f['handler']->getOption('in_fulltext_search')) {
                        $match_fields[] = $f['name'];
                    }
                }

                if (!$match_fields) {
                    return false;
                }

                $model->filterRelated($match_fields, $this->current_item['title']);

                break;
            case 'tags':

                $tags_exists = false;

                if (!empty($this->current_item['tags'])) {

                    $tag_model = cmsCore::getModel('tags', '_', false);

                    if ($tag_model) {

                        $tags_exists = true;

                        $tags = $tag_model->getTagsIDsForTarget('content', $this->ctype['name'], $this->current_item['id']);

                        if (!$tags) {
                            $tags_exists = false;
                        } else {

                            $model->join('tags_bind', 't', "t.target_id = i.id AND t.target_subject = '{$this->ctype['name']}' AND t.target_controller = 'content'")->filterIn('t.tag_id', $tags);

                            $model->orderByRaw(false);
                        }
                    }
                }

                if ($tags_exists) {
                    $model->filterCategory($this->ctype['name'], $this->current_item['category'], true, !empty($this->ctype['options']['is_cats_multi']));
                }

                break;
            case 'cat':

                $model->filterCategory($this->ctype['name'], $this->current_item['category'], true, !empty($this->ctype['options']['is_cats_multi']));

                break;
            default:
                break;
        }
    }

    private function applyRandomFilter($model) {

        $this->disableCache();

        $table = $model->getContentTypeTableName($this->ctype['name']);

        $model->joinQuery("(SELECT FLOOR(RAND() * (SELECT MAX(id) FROM {#}{$table})) AS id)", 'x', 'i.id >= x.id');

        $model->orderBy('i.date_pub');

        if ($this->current_item && $this->current_item['ctype_name'] === $this->ctype['name']) {
            $model->filterNotEqual('id', $this->current_item['id']);
        }
    }

    private function applyGroupOrUser($model) {

        // мы на странице группы?
        if ($this->getOption('auto_group') && $this->current_group) {

            $this->disableCache();

            $model->filterEqual('parent_id', $this->current_group['id'])->filterEqual('parent_type', 'group');
        }

        // мы на странице записи или профиля
        if ($this->getOption('auto_user')) {

            $user_id = $this->current_item['user_id'] ?? ($this->profile['id'] ?? null);
            if ($user_id) {

                $this->disableCache();

                $model->filterEqual('user_id', $user_id);

                if (!empty($this->current_item['id'])) {
                    $model->filterNotEqual('id', $this->current_item['id']);
                }

                if ($this->profile) {
                    $this->links = str_replace('{list_link}', href_to_profile($this->profile, ['content', $this->ctype['name']]), $this->links);
                }
            }
        }
    }

    private function applyPrivacy($model) {

        // применяем приватность
        // флаг показа только названий
        $this->hide_except_title = $model->applyPrivacyFilter($this->ctype, cmsUser::isAllowed($this->ctype['name'], 'view_all'));

        // Скрываем записи из скрытых родителей (приватных групп и т.п.)
        $model->enableHiddenParentsFilter();
    }

    private function applyRelation($model) {

        if (!$this->getOption('relation_id') || !$this->current_item || !$this->current_ctype) {
            return;
        }

        $condition = "r.parent_ctype_id = {$this->current_ctype['id']} AND " .
                "r.parent_item_id = {$this->current_item['id']} AND " .
                "r.child_ctype_id = {$this->ctype['id']} AND " .
                "r.child_item_id = i.id";

        $model->joinInner('content_relations_bind', 'r', $condition);

        $this->disableCache();

        $this->links = str_replace('{list_link}', href_to($this->current_ctype['name'], $this->current_item['slug'], "view-{$this->ctype['name']}"), $this->links);

    }

    private function applyContentFilter($model) {

        $filter_id = $this->getOption('filter_id');

        if ($filter_id) {

            $filter = $this->model_content->getContentFilter($this->ctype, $filter_id);

            if (!empty($filter['filters'])) {
                foreach ($filter['filters'] as $fname => $fvalue) {
                    if (isset($this->fields[$fname])) {
                        $this->fields[$fname]['handler']->applyFilter($model, $fvalue);
                    }
                }
            }
        }
    }

    private function applyDataset($model) {

        $dataset_id = $this->getOption('dataset');

        if ($dataset_id) {

            $dataset = $this->model_content->getContentDataset($dataset_id);

            if ($dataset) {
                $model->applyDatasetFilters($dataset);
            }
        }
    }

    private function applyCategory($model) {

        $cat_id = $this->getOption('category_id');

        if ($cat_id) {

            $category = $this->model_content->getCategory($this->ctype['name'], $cat_id);

            if ($category) {
                $model->filterCategory($this->ctype['name'], $category, true, !empty($this->ctype['options']['is_cats_multi']));
            }
        }
    }

    private function getItemInfoBar($item) {

        $bar = [];

        if (!empty($this->fields['date_pub']) &&
                in_array('date_pub', $this->visible_fields) &&
                $this->cms_user->isInGroups($this->fields['date_pub']['groups_read'])) {

            $bar['date_pub'] = [
                'css'   => 'bi_date_pub',
                'icon'  => 'calendar-alt',
                'html'  => $this->fields['date_pub']['html'] ?? $this->fields['date_pub']['handler']->parse($item['date_pub']),
                'title' => $this->fields['date_pub']['title']
            ];
        }

        if (!empty($this->fields['user']) &&
                in_array('user', $this->visible_fields) &&
                $this->cms_user->isInGroups($this->fields['user']['groups_read'])) {

            $bar['user'] = [
                'css'    => 'bi_user',
                'icon'   => 'user',
                'avatar' => $item['user']['avatar'] ?? [],
                'href'   => href_to_profile($item['user']),
                'html'   => $item['user']['nickname']
            ];
        }

        return $bar;
    }

}
