<?php
/**
 * @property \modelContent $model
 */
class onContentSubscriptionMatchList extends cmsAction {

    public $disallow_event_db_register = true;

    public function run($subscription, $items) {

        // результирующий список
        $match_list = [];

        // тип контента
        $ctype = $this->model->getContentTypeByName($subscription['subject']);
        if (!$ctype) {
            return $match_list;
        }

        // формируем id записей
        $item_ids = [];
        foreach ($items as $item) {
            $item_ids[] = $item['id'];
        }

        // категория
        $category = [];

        // фильтр по связи
        $relation_filter = '';

        // фильтрация по набору параметров
        // тут может быть и фильтр наборов
        // и кастомный фильтр
        $params = [];

        // Получаем поля для данного типа контента
        $fields = $this->model->getContentFields($ctype['name']);

        // фильтры по ячейкам таблицы
        if (!empty($subscription['params']['filters'])) {

            foreach ($subscription['params']['filters'] as $key => $filters) {

                // отдельные фильтры по некторым полям
                // связи
                if ($filters['field'] === 'relation' && !empty($filters['value']['parent_ctype_id'])) {

                    $parent_ctype = $this->model->getContentType($filters['value']['parent_ctype_id']);
                    if (!$parent_ctype) {
                        continue;
                    }

                    $_item = $this->model->getContentItem($parent_ctype['name'], $filters['value']['parent_item_id']);

                    if ($_item) {
                        $relation_filter = "r.parent_ctype_id = {$parent_ctype['id']} AND " .
                                "r.parent_item_id = {$_item['id']} AND " .
                                "r.child_ctype_id = {$ctype['id']} AND " .
                                "r.child_item_id = i.id";
                    }

                    continue;
                }
                // категория
                if ($filters['field'] === 'category_id') {

                    $category = $this->model->getCategory($ctype['name'], $filters['value']);

                    continue;
                }

                // проверяем наличие ячеек и заполняем фильтрацию
                if ($this->model->db->isFieldExists($this->model->table_prefix . $ctype['name'], $filters['field'])) {
                    $params[] = $filters;
                }
            }
        }

        // Получаем свойства
        $props = $props_fields = false;
        if (!empty($category['id']) && $category['id'] > 1) {
            $props = $this->model->getContentProps($ctype['name'], $category['id']);
            if ($props) {
                $props_fields = $this->getPropsFields($props);
            }
        }

        /**
         * Начинаем собирать запрос SQL
         */
        $this->model->limit(false);

        // нам нужны только записи, id которых передали
        $this->model->filterIn('id', $item_ids);

        // категория
        if (!empty($category['id']) && $category['id'] > 1) {

            // рекурсивность
            $is_recursive = true;
            if (array_key_exists('subscriptions_recursive_categories', $ctype['options'])) {
                if (!$ctype['options']['subscriptions_recursive_categories']) {
                    $is_recursive = false;
                }
            }

            $this->model->filterCategory($ctype['name'], $category, $is_recursive, !empty($ctype['options']['is_cats_multi']));
        }

        // фильтр по связям
        if ($relation_filter) {
            $this->model->joinInner('content_relations_bind', 'r', $relation_filter);
        }

        // фильтр по набору фильтров
        if ($params) {
            $this->model->applyDatasetFilters([
                'filters' => $params
            ], true);
        }

        // фильтрация по полям и свойствам
        if (!empty($subscription['params']['field_filters'])) {

            foreach ($subscription['params']['field_filters'] as $field_name => $field_value) {

                $matches = [];

                // свойства или поля
                if (preg_match('/^p([0-9]+)$/i', $field_name, $matches)) {

                    // нет свойств
                    if (!is_array($props)) {
                        continue;
                    }

                    // нет такого свойства
                    if (!isset($props_fields[$matches[1]])) {
                        continue;
                    }

                    $this->model->filterPropValue($ctype['name'], [
                        'id'      => $matches[1],
                        'handler' => $props_fields[$matches[1]]
                    ], $field_value);

                } else {

                    // нет такого поля
                    if (!isset($fields[$field_name])) {
                        continue;
                    }

                    $fields[$field_name]['handler']->applyFilter($this->model, $field_value);
                }
            }
        }

        $found_items = $this->model->getContentItems($ctype['name']);

        if ($found_items) {
            foreach ($found_items as $item) {
                $match_list[] = [
                    'url'       => href_to_abs($ctype['name'], $item['slug'] . '.html'),
                    'image_src' => '',
                    'title'     => $item['title']
                ];
            }
        }

        return $match_list;
    }

}
