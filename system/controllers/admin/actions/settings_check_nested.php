<?php
/**
 * @property \modelAdmin $model
 */
class actionAdminSettingsCheckNested extends cmsAction {

    public function run() {

        $db_nested_tables = cmsEventsManager::hookAll('db_nested_tables');

        $form = new cmsForm();

        $successful   = [];
        $unsuccessful = [];

        if ($db_nested_tables) {

            foreach ($db_nested_tables as $name => $tables) {

                $controller_title = string_lang('LANG_' . $name . '_CONTROLLER');

                $fieldset_id = $form->addFieldset($controller_title);

                foreach ($tables as $table_name => $title) {

                    $has_errors = $this->checkNestedSet($table_name);

                    if ($has_errors) {

                        $form->addField($fieldset_id,
                            new fieldCheckbox($table_name, [
                                'title'   => $title . ' => ' . $table_name,
                                'default' => 1
                            ])
                        );

                        $unsuccessful[$table_name] = $table_name;

                    } else {

                        $successful[$table_name] = $controller_title . ': ' . $title;
                    }
                }
            }
        }

        if ($unsuccessful && $this->request->has('submit')) {

            $repair_tables = $form->parse($this->request, true);

            $errors = $form->validate($this, $repair_tables);

            if (!$errors) {

                foreach ($repair_tables as $tname => $is_repair) {
                    if ($is_repair && isset($unsuccessful[$tname])) {
                        if ($this->repairNestedSet($tname) !== false) {
                            cmsUser::addSessionMessage(sprintf(LANG_CP_NS_FIX_SUCCESS, $tname), 'success');
                        } else {
                            cmsUser::addSessionMessage(sprintf(LANG_CP_NS_FIX_ERROR, $tname), 'error');
                        }
                    }
                }

                return $this->redirectToAction('settings/check_nested');
            }

            if ($errors) {
                cmsUser::addSessionMessage(LANG_FORM_ERRORS, 'error');
            }
        }

        return $this->cms_template->render('settings_check_nested', [
            'nested_tables_exists' => boolval($db_nested_tables),
            'successful'           => $successful,
            'unsuccessful'         => $unsuccessful,
            'form'                 => $form
        ]);
    }

    private function repairNestedSet($table, $i_value = 1, $k_parent = 0, $lvl = 0) {

        $db = $this->model->db;

        if (!is_numeric($k_parent) || !is_numeric($i_value)) {
            return false;
        }

        $r = $db->query("SELECT `id` FROM {#}{$table} WHERE `parent_id` = '{$k_parent}' ORDER BY `ns_left` asc, `ordering` asc", false, true);
        if (!$r) {
            return false;
        }

        $o = 1;

        while ($f = $db->fetchAssoc($r)) {

            $k_item  = $f['id'];
            $i_right = $this->repairNestedSet($table, $i_value + 1, $k_item, $lvl + 1);

            if ($i_right === false) {
                return false;
            }

            if (!$db->query("UPDATE {#}{$table} SET `ns_left` = '{$i_value}', `ns_right` = '{$i_right}', `ordering` = '" . $o++ . "', `ns_level` = '{$lvl}' where `id` = '{$k_item}'", false, true)) {
                return false;
            }

            $i_value = $i_right + 1;
        }

        return $i_value;
    }

    /**
     * Проверяет NS дерево
     *
     * @param string $table Имя таблицы
     * @return type
     */
    private function checkNestedSet(string $table) {

        $db = $this->model->db;

        $errors = [];

        // Шаг 1: ns_left >= ns_right
        $sql = "SELECT 1 FROM `{#}{$table}` WHERE `ns_left` >= `ns_right` LIMIT 1";
        $res = $db->query($sql, false, true);
        $errors[1] = ($res === false) ? true : ($db->numRows($res) > 0);

        // Шаг 2 и 3: проверка min_left и max_right
        $sql = "SELECT COUNT(`id`) AS rows_count, MIN(`ns_left`) AS min_left, MAX(`ns_right`) AS max_right FROM `{#}{$table}`";
        $result = $db->query($sql);
        if ($result !== false) {
            $data      = $db->fetchAssoc($result);

            $rows = (int)($data['rows_count'] ?? 0);
            $minL = isset($data['min_left'])  ? (int)$data['min_left']  : null;
            $maxR = isset($data['max_right']) ? (int)$data['max_right'] : null;

            $errors[2] = ($minL !== 1);         // левый край должен быть 1
            $errors[3] = ($maxR !== 2 * $rows); // правый край = 2*N

        } else {
            $errors[2] = true;
        }

        // Шаг 4: (ns_right - ns_left) должно быть НЕчётным
        $sql = "SELECT 1 FROM `{#}{$table}`
                WHERE MOD(`ns_right` - `ns_left`, 2) = 0
                LIMIT 1";
        $res = $db->query($sql, false, true);
        $errors[4] = ($res === false) ? true : ($db->numRows($res) > 0);

        // Шаг 5: паритет уровня
        $sql = "SELECT 1 FROM `{#}{$table}`
                WHERE MOD(`ns_left` - `ns_level` + 2, 2) = 0
                LIMIT 1";
        $res = $db->query($sql, false, true);
        $errors[5] = ($res === false) ? true : ($db->numRows($res) > 0);

        // Шаг 6: уникальность всех значений ns_left и ns_right
        // (каждое число в [1..max_right] встречается ровно 1 раз)
        $sql = "SELECT x.val
                FROM (
                    SELECT `ns_left` AS val
                    FROM `{#}{$table}`
                    UNION ALL
                    SELECT `ns_right`
                    FROM `{#}{$table}`
                ) x
                GROUP BY x.val
                HAVING COUNT(*) <> 1
                LIMIT 1";
        $res = $db->query($sql, false, true);
        $errors[6] = ($res === false) ? true : ($db->numRows($res) > 0);

        return in_array(true, $errors, true);
    }

}
