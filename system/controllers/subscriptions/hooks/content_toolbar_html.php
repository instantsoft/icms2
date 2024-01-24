<?php

class onSubscriptionsContentToolbarHtml extends cmsAction {

    public function run($data) {

        list($ctype_name, $category, $current_dataset, $filters) = $data;

        $ctype = $this->model_content->getContentTypeByName($ctype_name);
        if (!$ctype) {
            return '';
        }

        if (array_key_exists('enable_subscriptions', $ctype['options'])) {
            if (!$ctype['options']['enable_subscriptions']) {
                return '';
            }
        }

        // если есть фильтрация по юзеру, не показываем автору
        if ($filters) {
            foreach ($filters as $fkey => $f) {
                if ($f['field'] === 'user_id' && $f['value'] == $this->cms_user->id) {
                    return '';
                }
                if ($f['value'] === false) {
                    unset($filters[$fkey]);
                }
            }
        }

        $params = [
            'field_filters' => [],
            'filters'       => $filters,
            'dataset'       => []
        ];

        if (!empty($category['id'])) {
            $params['filters'][] = [
                'field'     => 'category_id',
                'condition' => 'eq',
                'value'     => (string) $category['id']
            ];
        }

        if (!empty($current_dataset['filters'])) {

            $dataset_filters = [];

            foreach ($current_dataset['filters'] as $filter) {

                if (!isset($filter['value'])) {
                    continue;
                }
                if ($filter['value'] === false) {
                    continue;
                }
                if (($filter['value'] === '') && !in_array($filter['condition'], ['nn', 'ni'])) {
                    continue;
                }
                if (empty($filter['condition'])) {
                    continue;
                }

                if ($filter['value'] !== '') {
                    $filter['value'] = (string) string_replace_user_properties($filter['value']);
                }

                $params['filters'][] = $filter;
                $dataset_filters[]   = $filter['field'];
            }

            if ($dataset_filters) {
                $params['dataset'] = [
                    'id'     => (string) $current_dataset['id'],
                    'fields' => $dataset_filters
                ];
            }
        }

        return $this->renderSubscribeButton([
            'controller' => 'content',
            'subject'    => $ctype_name,
            'params'     => $params
        ]);
    }

}
