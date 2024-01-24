<?php
/**
 * @property \modelContent $model
 * @property \modelGroups $model_groups
 * @property \modelUsers model_users
 */
class onContentSubscribeListTitle extends cmsAction {

    public $disallow_event_db_register = true;

    public function run($target, $subscribe) {

        $ctype = $this->model->getContentTypeByName($target['subject']);
        if(!$ctype){
            return false;
        }

        $result_title = $ctype['title'];
        $titles = [];

        // нет фильтров
        if(empty($target['params']['filters']) && empty($target['params']['field_filters'])){
            return $result_title;
        }

        // id категории для свойств
        $category_id = 0;

        // Получаем поля для данного типа контента
        $fields = $this->model->getContentFields($ctype['name']);

        if (!empty($target['params']['filters'])) {

            foreach ($target['params']['filters'] as $key => $filters) {

                if (!is_numeric($key)) {
                    return false;
                }

                // пользователь
                if ($filters['field'] === 'user_id') {

                    if (is_array($filters['value'])) {
                        return false;
                    }

                    $user = $this->model_users->getUser($filters['value']);
                    if (!$user) {
                        return false;
                    }

                    $titles[] = $user['nickname'];

                    continue;
                }
                // папка
                if ($filters['field'] === 'folder_id') {

                    if (is_array($filters['value'])) {
                        return false;
                    }

                    $folder = $this->model->getContentFolder($filters['value']);
                    if (!$folder) {
                        return false;
                    }

                    $titles[] = mb_strtolower($folder['title']);

                    continue;
                }
                // группа
                if ($filters['field'] === 'parent_id' &&
                        !empty($target['params']['filters'][$key + 1]['value']) &&
                        $target['params']['filters'][$key + 1]['value'] === 'group') {

                    if (is_array($filters['value'])) {
                        return false;
                    }

                    $group = $this->model_groups->getGroup($filters['value']);
                    if (!$group) {
                        return false;
                    }

                    $titles[] = mb_strtolower($group['title']);

                    continue;
                }
                // связь
                if ($filters['field'] === 'relation') {

                    if (!is_array($filters['value']) ||
                            empty($filters['value']['parent_ctype_id']) ||
                            empty($filters['value']['parent_item_id']) ||
                            empty($filters['value']['child_ctype_id']) ||
                            !is_numeric($filters['value']['child_ctype_id']) ||
                            !is_numeric($filters['value']['parent_ctype_id']) ||
                            !is_numeric($filters['value']['parent_item_id'])) {
                        return false;
                    }

                    $item = $this->model->getContentItem($filters['value']['parent_ctype_id'], $filters['value']['parent_item_id']);
                    if (!$item) {
                        return false;
                    }

                    // для связей стартовое название меняем на родительское
                    $parent_ctype = $this->model->getContentType($filters['value']['parent_ctype_id']);

                    $result_title = $parent_ctype['title'];

                    $titles[] = mb_strtolower($item['title']);

                    $child_ctype = $this->model->getContentType($filters['value']['child_ctype_id']);
                    if (!$child_ctype) {
                        return false;
                    }

                    $titles[] = mb_strtolower($child_ctype['title']);

                    continue;
                }
                // категория
                if ($filters['field'] === 'category_id') {

                    if (is_array($filters['value'])) {
                        return false;
                    }

                    $cat = $this->model->getCategory($ctype['name'], $filters['value']);
                    if (!$cat) {
                        return false;
                    }

                    $titles[] = mb_strtolower($cat['title']);

                    $category_id = $cat['id'];

                    continue;
                }

                if (isset($fields[$filters['field']])) {

                    if (is_array($filters['value'])) {
                        return false;
                    }

                    $result = '';

                    if (!empty($filters['condition'])) {

                        switch ($filters['condition']) {

                            case 'gt': $result = '&gt; ' . $filters['value'];
                                break;
                            case 'lt': $result = '&lt; ' . $filters['value'];
                                break;
                            case 'ge': $result = '&ge; ' . $filters['value'];
                                break;
                            case 'le': $result = '&le; ' . $filters['value'];
                                break;
                            case 'nn': $result = LANG_FILTER_NOT_NULL;
                                break;
                            case 'ni': $result = LANG_FILTER_IS_NULL;
                                break;
                            case 'lk': $result = LANG_FILTER_LIKE . ' ' . $filters['value'];
                                break;
                            case 'ln': $result = LANG_FILTER_NOT_LIKE . ' ' . $filters['value'];
                                break;
                            case 'lb': $result = LANG_FILTER_LIKE_BEGIN . ' ' . $filters['value'];
                                break;
                            case 'lf': $result = LANG_FILTER_LIKE_END . ' ' . $filters['value'];
                                break;
                            case 'dy': $result = LANG_FILTER_DATE_YOUNGER . ' ' . $filters['value'];
                                break;
                            case 'do': $result = LANG_FILTER_DATE_OLDER . ' ' . $filters['value'];
                                break;
                        }
                    }

                    if (!$result) {
                        $result = $fields[$filters['field']]['handler']->
                                setItem(['ctype_name' => $ctype['name'], 'ctype' => $ctype, 'id' => 0])->
                                getStringValue($filters['value']);
                    }

                    if (!$result) {
                        return false;
                    }

                    $titles[] = mb_strtolower($fields[$filters['field']]['title'] . ' ' . $result);
                }
            }

        }

        // Получаем поля-свойства
        $props = $props_fields = false;
        if ($category_id > 1) {
            $props = $this->model->getContentProps($ctype['name'], $category_id);
            if ($props) {
                $props_fields = $this->getPropsFields($props);
            }
        }

        if (!empty($target['params']['field_filters'])) {

            $request = new cmsRequest($target['params']['field_filters']);

            foreach ($target['params']['field_filters'] as $field_name => $field_value) {

                $matches = [];

                // свойства или поля
                if (preg_match('/^p([0-9]+)$/i', $field_name, $matches)) {

                    // нет свойств
                    if (!is_array($props)) {
                        return false;
                    }

                    // нет такого свойства
                    if (!isset($props_fields[$matches[1]])) {
                        return false;
                    }

                    $handler = $props_fields[$matches[1]];

                    $field_title = $props[$matches[1]]['title'];

                } else {

                    // нет такого поля
                    if (!isset($fields[$field_name])) {
                        return false;
                    }

                    $handler = $fields[$field_name]['handler'];

                    $field_title = $fields[$field_name]['title'];
                }

                $handler->setItem(['ctype_name' => $ctype['name'], 'ctype' => $ctype, 'id' => 0])->setContext('filter');

                $value = $handler->storeFilter($request->get($field_name, false, $handler->getDefaultVarType()));

                $result = $handler->getStringValue($value);

                if ($result) {
                    $titles[] = mb_strtolower($field_title . ' ' . $result);
                }
            }
        }

        if (!empty($titles)) {
            $result_title .= ' — ' . implode(', ', array_unique($titles));
        }

        return $result_title;
    }

}
