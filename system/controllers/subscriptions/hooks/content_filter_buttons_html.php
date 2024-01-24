<?php

class onSubscriptionsContentFilterButtonsHtml extends cmsAction {

    public function run($data) {

        list($ctype_name, $form_url, $filters) = $data;

        $ctype = $this->model_content->getContentTypeByName($ctype_name);
        if (!$ctype) {
            return '';
        }

        if (array_key_exists('enable_subscriptions', $ctype['options'])) {
            if (!$ctype['options']['enable_subscriptions']) {
                return '';
            }
        }

        // Чтобы в фильтрах привести всё к строкам
        array_walk_recursive($filters, function (&$val, $key) {
            $val = (string) $val;
        });

        $params = [
            'field_filters' => $filters,
            'filters'       => [],
            'dataset'       => []
        ];

        $cat = cmsModel::getCachedResult('current_ctype_category');

        if (!empty($cat['id'])) {
            $params['filters'][] = [
                'field'     => 'category_id',
                'condition' => 'eq',
                'value'     => (string) $cat['id']
            ];
        }

        $profile = cmsModel::getCachedResult('current_profile');

        if (!empty($profile['id'])) {
            $params['filters'][] = [
                'field'     => 'user_id',
                'condition' => 'eq',
                'value'     => (string) $profile['id']
            ];
        }

        $group = cmsModel::getCachedResult('current_group');

        if (!empty($group['id'])) {
            $params['filters'][] = [
                'field'     => 'parent_id',
                'condition' => 'eq',
                'value'     => (string) $group['id']
            ];
            $params['filters'][] = [
                'field'     => 'parent_type',
                'condition' => 'eq',
                'value'     => 'group'
            ];
        }

        $child_ctype = cmsModel::getCachedResult('current_child_ctype');

        if (!empty($child_ctype['id'])) {

            $ctype = cmsModel::getCachedResult('current_ctype');
            $item  = cmsModel::getCachedResult('current_ctype_item');

            if (!empty($ctype['id']) && !empty($item['id'])) {

                $params['filters'][] = [
                    'field'     => 'relation',
                    'condition' => 'inner',
                    'value'     => [
                        'parent_ctype_id' => $ctype['id'],
                        'parent_item_id'  => $item['id'],
                        'child_ctype_id'  => $child_ctype['id']
                    ]
                ];
            }
        }

        $current_dataset = cmsModel::getCachedResult('current_ctype_dataset');

        if (!empty($current_dataset['filters'])) {

            $dataset_filters = [];

            foreach ($current_dataset['filters'] as $filter) {

                if (!isset($filter['value'])) {
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

                $dataset_filters[] = $filter['field'];
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
