<?php

class cmsPermissions {

    /**
     * Добавляет правило доступа в каталог правил
     * @param string $controller Название контроллера
     * @param array $rule Массив данных правила
     * @return integer|false
     */
    static function addRule($controller, $rule) {

        $core = cmsCore::getInstance();

        if ($core->db->getRowsCount('perms_rules', "controller = '{$controller}' AND name = '{$rule['name']}'", 1)) {
            return false;
        }

        if (!in_array($rule['type'], ['flag', 'list', 'number'])) {
            $rule['type'] = 'flag';
        }

        $sql = "INSERT INTO {#}perms_rules (controller, name, type, options)
                VALUES ('{$controller}', '{$rule['name']}', '{$rule['type']}', '{$rule['options']}')";

        $core->db->query($sql, false, true);

        $rule_id = ($core->db->error()) ? false : $core->db->lastId('perms_rules');

        return $rule_id;
    }

    /**
     * Возвращает список доступных правил доступа для указанного компонента
     * @param string $controller Название контроллера
     * @return array
     */
    static function getRulesList($controller) {

        $model = new cmsModel();

        $model->filterEqual('controller', $controller);

        cmsCore::loadControllerLanguage($controller);

        return $model->orderBy('name')->get('perms_rules', function ($rule, $model) {

            $title_const = 'LANG_RULE_' . strtoupper($rule['controller']) . '_' . strtoupper($rule['name']);
            $hint_const  = 'LANG_RULE_' . strtoupper($rule['controller']) . '_' . strtoupper($rule['name']) . '_HINT';

            $rule['title']      = defined($title_const) ? constant($title_const) : (!empty($rule['title']) ? $rule['title'] : $title_const);
            $rule['title_hint'] = defined($hint_const) ? constant($hint_const) : '';

            if ($rule['type'] === 'list' && $rule['options']) {

                $rule['options'] = explode(',', $rule['options']);

                $options    = [];
                $options[0] = LANG_PERM_OPTION_NULL;

                foreach ($rule['options'] as $id => $option) {
                    $options[trim($option)] = constant('LANG_PERM_OPTION_' . strtoupper(trim($option)));
                }

                $rule['options'] = $options;
            }

            return $rule;
        }) ?: [];
    }

    /**
     * Возвращает значения всех правил доступа для указанного субъекта действия
     * @param string $subject
     * @return array
     */
    static function getPermissions($subject = false) {

        $model = new cmsModel();

        if ($subject) {
            $model->filterEqual('subject', $subject);
        }

        $items = $model->get('perms_users', false, false);
        if (!$items) {
            return false;
        }

        $values = [];

        foreach ($items as $item) {

            $values[$item['rule_id']][$item['group_id']] = $item['value'];
        }

        return $values;
    }

    /**
     * Возвращает правила доступа для переданных id групп
     * @param array $user_groups id групп
     * @return array
     */
    static function getUserPermissions($user_groups) {

        $model = new cmsModel();

        $model->filterIn('group_id', $user_groups);

        return self::getPermissionsData($model);
    }

    static function getRuleSubjectPermissions($controller, $subject, $permission) {

        $model = new cmsModel();

        $model->filterEqual('r.controller', $controller)->
                filterEqual('r.name', $permission)->
                filterEqual('subject', $subject);

        return self::getPermissionsData($model);
    }

    private static function getPermissionsData($model) {

        $model->select('r.name', 'rule_name');
        $model->select('r.type', 'rule_type');
        $model->select('r.options', 'rule_options');

        $model->joinInner('perms_rules', 'r FORCE INDEX (PRIMARY ) ', 'r.id = i.rule_id');

        $items = $model->get('perms_users', false, false);
        if (!$items) {
            return [];
        }

        $values = [];

        foreach ($items as $item) {

            //
            // Для правил, которые являются списками важен порядок опций
            // Здесь мы проверяем, что более приоритетная опция не была
            // уже присвоена ранее
            // Такое может быть, если пользователь состоит в нескольких
            // группах, тогда будет браться самая приоритетная из всех
            // доступных опций (значений) этого правила
            //
            if ($item['rule_type'] == 'list') {

                $rule_options = explode(',', $item['rule_options']);

                if (isset($values[$item['subject']][$item['rule_name']])) {
                    $current_value    = $values[$item['subject']][$item['rule_name']];
                    $current_priority = array_search($current_value, $rule_options);
                    $next_priority    = array_search($item['value'], $rule_options);
                    if ($current_priority >= $next_priority) {
                        continue;
                    }
                }
            }

            $values[$item['subject']][$item['rule_name']] = $item['value'];
        }

        return $values;
    }

    /**
     * Возвращает массив контроллеров, для которых есть правила доступа
     * @return array
     */
    static function getControllersWithRules() {

        $model = new cmsModel();

        $model->groupBy('controller');

        return $model->get('perms_rules', function ($rule, $model) {
            return $rule['controller'];
        }, false);
    }

    /**
     * Сохраняет значения правил для субъекта
     * @param string $subject Субъект действия правила
     * @param array $perms Правила и их значения
     */
    static function savePermissions($subject, $perms) {

        $model = new cmsModel();

        foreach ($perms as $rule_id => $values) {

            if (is_null($values)) {
                $model->filterEqual('subject', $subject)
                        ->filterEqual('rule_id', $rule_id)
                        ->deleteFiltered('perms_users');
                continue;
            }

            foreach ($values as $group_id => $value) {

                $model->filterEqual('subject', $subject)
                        ->filterEqual('rule_id', $rule_id)
                        ->filterEqual('group_id', $group_id)
                        ->lockFilters();

                if (!$value) {
                    $model->deleteFiltered('perms_users');
                    $model->unlockFilters();
                    $model->resetFilters();
                    continue;
                }

                $is_exists = $model->getFieldFiltered('perms_users', 'value');

                $model->unlockFilters();

                if ($is_exists) {

                    $model->updateFiltered('perms_users', [
                        'rule_id'  => $rule_id,
                        'group_id' => $group_id,
                        'subject'  => $subject,
                        'value'    => $value
                    ]);
                } else {

                    $model->insert('perms_users', [
                        'rule_id'  => $rule_id,
                        'group_id' => $group_id,
                        'subject'  => $subject,
                        'value'    => $value
                    ]);
                }
            }

            $model->resetFilters();
        }

    }

    /**
     * Возвращает пользователей групп, для которых переданное правило включено
     * или установлено в значение $value
     *
     * @param string $controller Название компонента
     * @param string $name Название правила
     * @param mixed $value Значение правила. Если не передано, значение не учитывается
     * @param string $subject Субъект правила. Если не передан, то одинаков с компонентом
     *
     * @return array Массив пользователей
     */
    public static function getRulesGroupMembers($controller, $name, $value = false, $subject = false) {

        if (!$subject) {
            $subject = $controller;
        }

        $model = new cmsModel();

        $rule = $model->filterEqual('controller', $controller)->filterEqual('name', $name)->getItem('perms_rules');
        if (!$rule) {
            return [];
        }

        $model->filterEqual('subject', $subject)->filterEqual('rule_id', $rule['id']);
        if ($value) {
            $model->filterEqual('value', $value);
        }

        $groups_ids = $model->selectOnly('group_id')->get('perms_users', function ($item, $model) {
            return $item['group_id'];
        }, 'group_id');

        if (!$groups_ids) {
            return [];
        }

        return $model->filterIn('group_id', $groups_ids)->
                selectOnly('i.user_id', 'id')->
                joinUser('user_id', [
                    'u.notify_options' => 'notify_options',
                    'u.email'          => 'email',
                    'u.slug'           => 'slug',
                    'u.nickname'       => 'nickname',
                    'u.avatar'         => 'avatar'
                ])->joinSessionsOnline()->get('{users}_groups_members', function ($item, $model) {

            $item['notify_options'] = cmsModel::yamlToArray($item['notify_options']);

            return $item;
        }) ?: [];
    }

}
