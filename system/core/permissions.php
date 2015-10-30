<?php

class cmsPermissions {

    /**
     * Добавляет правило доступа в каталог правил
     * @param string $controller
     * @param array $rule
     * @return int
     */
    static function addRule($controller, $rule){

        $core = cmsCore::getInstance();

        $rule['is_required'] = isset($rule['is_required']) ? intval($rule['is_required']) : 0;

        if (!in_array($rule['type'], array('flag', 'list', 'number'))){ $rule['type'] = 'flag'; }

        $sql = "INSERT INTO {#}perms_rules (controller, name, type, options, is_required)
                VALUES ('{$controller}', '{$rule['name']}', '{$rule['type']}', '{$rule['options']}', '{$rule['is_required']}')";

        $core->db->query($sql);

        $rule_id = ($core->db->error()) ? false : $core->db->lastId('perms_rules');

        return $rule_id;

    }

//============================================================================//
//============================================================================//

    /**
     * Возвращает список доступных правил доступа для указанного компонента
     * @param string $controller
     * @return array
     */
    static function getRulesList($controller){

        $model = new cmsModel();

        $model->filterEqual('controller', $controller);

        cmsCore::loadControllerLanguage($controller);

        $rules = $model->orderBy('name')->get('perms_rules', function($rule, $model){

            $rule['title'] = constant('LANG_RULE_'.mb_strtoupper($rule['controller']).'_'.mb_strtoupper($rule['name']));

            if ($rule['type'] == 'list' && $rule['options']){
                $rule['options'] = explode(',', $rule['options']);
                $options = array();
                $options[0] = LANG_PERM_OPTION_NULL;
                foreach($rule['options'] as $id=>$option){
                    $options[trim($option)] = constant("LANG_PERM_OPTION_".mb_strtoupper(trim($option)));
                }
                $rule['options'] = $options;
            }

            return $rule;

        });

        return $rules;

    }

//============================================================================//
//============================================================================//

    /**
     * Возвращает значения всех правил доступа для указанного контроллера
     * и всех групп пользователей
     * @param string $controller
     * @param string $subject
     * @return array
     */
    static function getPermissions($subject=false){

        $model = new cmsModel();

        if ($subject){
            $model->filterEqual('subject', $subject);
        }

        $items = $model->get('perms_users', false, false);

        if (!$items) { return false; }

        $values = array();

        foreach($items as $item){

            $values[$item['rule_id']][$item['group_id']] = $item['value'];

        }

        return $values;

    }

//============================================================================//
//============================================================================//

    static function getUserPermissions($user_groups) {

        $model = new cmsModel();

        $model->filterIn('group_id', $user_groups);

        $model->select('r.name', 'rule_name');
        $model->select('r.type', 'rule_type');
        $model->select('r.options', 'rule_options');

        $model->joinInner('perms_rules', 'r FORCE INDEX (PRIMARY ) ', 'r.id = i.rule_id');

        $items = $model->get('perms_users', false, false);

        if (!$items) { return false; }

        $values = array();

        foreach($items as $item){

            //
            // Для правил, которые являются списками важен порядок опций
            // Здесь мы проверяем, что более приоритетная опция не была
            // уже присвоена ранее
            // Такое может быть, если пользователь состоит в нескольких
            // группах, тогда будет браться самая приоритетная из всех
            // доступных опций (значений) этого правила
            //
            if ($item['rule_type'] == 'list'){

                $rule_options = explode(',', $item['rule_options']);

                if (isset($values[$item['subject']][$item['rule_name']])){
                    $current_value      = $values[$item['subject']][$item['rule_name']];
                    $current_priority   = array_search($current_value, $rule_options);
                    $next_priority      = array_search($item['value'], $rule_options);
                    if ($current_priority >= $next_priority) { continue; }
                }

            }

            $values[$item['subject']][$item['rule_name']] = $item['value'];

        }

        return $values;

    }

//============================================================================//
//============================================================================//

    static function getControllersWithRules(){

        $model = new cmsModel();

        $model->groupBy('controller');

        $controllers = $model->get('perms_rules', function($rule, $model){

            $controller = $rule['controller'];
            return $controller;

        }, false);

        return $controllers;

    }

//============================================================================//
//============================================================================//

    static function savePermissions($subject, $perms){

        $model = new cmsModel();

        foreach($perms as $rule_id => $values){

            if (is_null($values)) {
                $model->filterEqual('subject', $subject)
                        ->filterEqual('rule_id', $rule_id)
                        ->deleteFiltered('perms_users');
                continue;
            }

            foreach($values as $group_id => $value){

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

                if ($is_exists){
                    $model->updateFiltered('perms_users', array(
                        'rule_id' => $rule_id,
                        'group_id' => $group_id,
                        'subject' => $subject,
                        'value' => $value
                    ));
                } else {
                    $model->insert('perms_users', array(
                        'rule_id' => $rule_id,
                        'group_id' => $group_id,
                        'subject' => $subject,
                        'value' => $value
                    ));
                }

            }

            $model->resetFilters();

        }

    }

//============================================================================//
//============================================================================//

//============================================================================//
//============================================================================//



}