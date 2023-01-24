<?php

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

    private function checkNestedSet($table, $differ = '') {

        $errors = [];

        $db = $this->model->db;

        $differ = $db->escape($differ);

        // Шаг 1
        $sql = "SELECT id FROM {#}{$table} WHERE `ns_left` >= `ns_right` AND `ns_differ` = '{$differ}'";

        $result = $db->query($sql, false, true);
        if ($result !== false) {
            $errors[1] = $db->numRows($result) > 0;
        } else {
            $errors[1] = true;
        }

        // Шаг 2 и 3
        $sql = "SELECT COUNT(`id`) as rows_count, MIN(`ns_left`) as min_left, MAX(`ns_right`) as max_right FROM {#}{$table} WHERE `ns_differ` = '{$differ}'";

        $result = $db->query($sql);
        if ($result !== false) {
            $data      = $db->fetchAssoc($result);
            $errors[2] = $data['min_left'] != 1;
            $errors[3] = $data['max_right'] != 2 * $data['rows_count'];
        } else {
            $errors[2] = true;
        }

        // Шаг 4
        $sql = "SELECT `id`, `ns_right`, `ns_left`
				FROM {#}{$table}
                WHERE MOD((`ns_right`-`ns_left`), 2) = 0 AND `ns_differ` = '{$differ}'";

        $result = $db->query($sql, false, true);
        if ($result !== false) {
            $errors[4] = $db->numRows($result) > 0;
        } else {
            $errors[4] = true;
        }

        // Шаг 5
        $sql = "SELECT `id`
				FROM {#}{$table}
				WHERE MOD((`ns_left`-`ns_level`+2), 2) = 0 AND `ns_differ` = '$differ'";

        $result = $db->query($sql, false, true);
        if ($result !== false) {
            $errors[5] = $db->numRows($result) > 0;
        } else {
            $errors[5] = true;
        }

        // Шаг 6
        $sql = "SELECT 	t1.id,
						COUNT(t1.id) AS rep,
						MAX(t3.ns_right) AS max_right
				FROM {#}{$table} AS t1, {#}{$table} AS t2, {#}{$table} AS t3
				WHERE t1.ns_left <> t2.ns_left AND t1.ns_left <> t2.ns_right AND t1.ns_right <> t2.ns_left AND t1.ns_right <> t2.ns_right
                        AND t1.ns_differ = '{$differ}' AND t2.ns_differ = '{$differ}' AND t3.ns_differ = '{$differ}'
				GROUP BY t1.id
				HAVING max_right <> SQRT(4 * rep + 1) + 1";

        $result = $db->query($sql, false, true);
        if ($result !== false) {
            $errors[6] = $db->numRows($result) > 0;
        } else {
            $errors[6] = true;
        }

        return in_array(true, $errors);
    }

}
