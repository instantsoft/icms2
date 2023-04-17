<?php
/**
 * @deprecated
 * Экшен получения списка записей должен
 * так же сохранять поле, см. пример /system/traits/controllers/actions/listgrid.php
 */
class actionAdminInlineSave extends cmsAction {

    public function run($table = null, $item_id = null, $disable_language_context = null) {

        header('X-Frame-Options: DENY');

        if (!$this->request->isAjax()) {
            return cmsCore::error404();
        }

        if (!cmsForm::validateCSRFToken($this->request->get('csrf_token', ''))) {
            return $this->cms_template->renderJSON([
                'error' => LANG_ERROR . ' #csrf_token'
            ]);
        }

        if (!$item_id || !$table || !is_numeric($item_id) || $this->validate_regexp('/^([a-z0-9\_{}]*)$/', urldecode($table)) !== true) {
            return $this->cms_template->renderJSON([
                'error' => LANG_ERROR . ' #validate'
            ]);
        }

        $data = $this->request->get('data', []);
        if (!$data) {

            return $this->cms_template->renderJSON([
                'error' => LANG_ERROR . ' #empty data'
            ]);
        }

        if (!$this->model->db->isTableExists($table)) {

            return $this->cms_template->renderJSON([
                'error' => LANG_ERROR . ' #table'
            ]);
        }

        $i = $this->model->getItemByField($table, 'id', $item_id);
        if (!$i) {

            return $this->cms_template->renderJSON([
                'error' => LANG_ERROR . ' #404'
            ]);
        }

        $table_fields = $this->model->db->getTableFieldsTypes($table);

        $_data = [];

        foreach ($data as $field => $value) {

            if (!array_key_exists($field, $i) || is_array($data[$field])) {

                unset($data[$field]);

                continue;
            }

            if(!$disable_language_context){

                // Перевод
                $field_tr = $this->model->getTranslatedFieldName($field, $table);
                // Обновиться должно только поле с переводом
                if($field_tr !== $field){
                    unset($data[$field]);
                }

            } else {

                $field_tr = $field;
            }

            if ($value) {

                $table_field_type = $table_fields[$field];

                if (in_array($table_field_type, ['tinyint', 'smallint', 'mediumint', 'int', 'bigint'])) {

                    $_data[$field] = $data[$field_tr] = intval($value);

                } elseif (in_array($table_field_type, ['decimal', 'float', 'double'])) {

                    $_data[$field] = $data[$field_tr]  = floatval(str_replace(',', '.', trim($value)));

                } else {

                    $data[$field_tr]  = strip_tags($value);

                    $_data[$field] = htmlspecialchars($data[$field_tr]);
                }

            } else {
                $_data[$field] = $value;
            }
        }

        if (empty($data)) {

            return $this->cms_template->renderJSON([
                'error' => LANG_ERROR . ' #empty data'
            ]);
        }

        list($data, $_data, $i) = cmsEventsManager::hook('admin_inline_save', [$data, $_data, $i]);
        list($data, $_data, $i) = cmsEventsManager::hook('admin_inline_save_' . str_replace(['{', '}'], '', $table), [$data, $_data, $i]);

        $this->model->update($table, $item_id, $data);

        list($data, $_data, $i) = cmsEventsManager::hook('admin_inline_save_after', [$data, $_data, $i]);
        list($data, $_data, $i) = cmsEventsManager::hook('admin_inline_save_after_' . str_replace(['{', '}'], '', $table), [$data, $_data, $i]);

        return $this->cms_template->renderJSON([
            'error'  => false,
            'info'   => LANG_SUCCESS_MSG,
            'values' => $_data
        ]);
    }

}
