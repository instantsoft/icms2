<?php

class onSubscriptionsContentToolbarHtml extends cmsAction {

    public function run($data){

        list($ctype_name, $category, $current_dataset, $filters) = $data;

        $params = array(
            'field_filters' => array(),
            'filters' => $filters
        );

        if(!empty($category['id'])){
            $params['filters'][] = array(
                'field'     => 'category_id',
                'condition' => 'eq',
                'value'     => $category['id']
            );
        }

        if(!empty($current_dataset['filters'])){
            foreach($current_dataset['filters'] as $filter){

                if (!isset($filter['value'])) { continue; }
                if (($filter['value'] === '') && !in_array($filter['condition'], array('nn', 'ni'))) { continue; }
                if (empty($filter['condition'])) { continue; }

                if ($filter['value'] !== '') { $filter['value'] = string_replace_user_properties($filter['value']); }

                $params['filters'][] = $filter;

            }
        }

        return $this->renderSubscribeButton(array(
            'controller' => 'content',
            'subject'    => $ctype_name,
            'params'     => $params
        ));

    }

}
