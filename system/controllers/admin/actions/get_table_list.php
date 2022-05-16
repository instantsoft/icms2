<?php

class actionAdminGetTableList extends cmsAction {

    public function run($table, $id_field = null, $title_field = null, $default = 'zero') {

        header('X-Frame-Options: DENY');

        if (!$this->request->isAjax()) {
            return cmsCore::error404();
        }

        if (!$table || $this->validate_regexp('/^([a-z0-9\_{}]*)$/', urldecode($table)) !== true) {
            return $this->cms_template->renderJSON([
                'error' => LANG_ERROR . ' #validate table'
            ]);
        }

        if (!$this->model->db->isTableExists($table)) {
            return $this->cms_template->renderJSON([
                'error' => LANG_ERROR . ' #table not exists'
            ]);
        }

        if (!$id_field) {
            $id_field = 'id';
        }

        if (!$title_field) {
            $title_field = 'title';
        }

        $filter_field_value = $this->request->get('value', '');

        $filter_field_name = $this->request->get('filter_field_name', '');
        if (!$filter_field_name) {
            return cmsCore::error404();
        }

        $table_fields = $this->model->db->getTableFields($table);

        if (!in_array($id_field, $table_fields, true)) {
            return $this->cms_template->renderJSON([
                'error' => LANG_ERROR . ' #id_field'
            ]);
        }

        if (!in_array($title_field, $table_fields, true)) {
            return $this->cms_template->renderJSON([
                'error' => LANG_ERROR . ' #title_field'
            ]);
        }

        if (!in_array($filter_field_name, $table_fields, true)) {
            return $this->cms_template->renderJSON([
                'error' => LANG_ERROR . ' #filter_field_name'
            ]);
        }

        // Основной фильтр
        $this->model->filterEqual($filter_field_name, $filter_field_value);

        // Дополнительные фильтры
        $filters = $this->request->get('filters', []);

        if ($filters) {
            $this->model->applyDatasetFilters(['filters' => $filters], true, $table_fields);
        }

        $items = $this->model->selectOnly($id_field)->select($title_field)->get($table, function ($item, $model) use ($title_field) {
            return $item[$title_field];
        }, $id_field);

        if ($default === 'zero') {
            $list = ['0' => ''];
        } elseif ($default === 'empty') {
            $list = ['' => ''];
        } else {
            $list = [];
        }

        if ($items) {
            foreach ($items as $id => $title) {
                $list[] = ['title' => $title, 'value' => $id];
            }
        }

        return $this->cms_template->renderJSON($list);
    }

}
