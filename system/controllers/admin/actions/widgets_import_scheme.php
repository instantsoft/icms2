<?php

class actionAdminWidgetsImportScheme extends cmsAction {

    public function run($to_template_name) {

        $this->model->localizedOff();

        $form = $this->getForm('widgets_import_scheme', [$to_template_name]);

        $is_submitted = $this->request->has('csrf_token');

        $data = $form->parse($this->request, $is_submitted);

        if ($is_submitted) {

            $errors = $form->validate($this, $data);

            if (!$errors) {

                // Если загрузка файла
                if($data['yaml_file']){
                    $success = $this->importFromFile($data, $to_template_name);
                } else {
                    // Копирование с существующего шаблона
                    $success = $this->copyFromExistsTemplate($data, $to_template_name);
                }

                if($success !== true){
                    return $this->cms_template->renderJSON($success);
                }

                cmsUser::addSessionMessage(LANG_CP_WIDGETS_LAYOUT_ISUCCESS, 'success');

                return $this->cms_template->renderJSON([
                    'errors'       => false,
                    'redirect_uri' => href_to('admin', 'widgets') . '?template_name=' . $to_template_name
                ]);
            }

            if ($errors) {
                return $this->cms_template->renderJSON([
                    'errors' => $errors
                ]);
            }
        }

        return $this->cms_template->render([
            'template_name' => $to_template_name,
            'data'          => $data,
            'form'          => $form,
            'errors'        => false
        ]);
    }

    private function deleteLayout($template_name) {

        $rows = $this->model->filterEqual('template', $template_name)->get('layout_rows');

        if(!$rows){
            return false;
        }

        // Удаляем все ряды
        $this->model->filterEqual('template', $template_name)->deleteFiltered('layout_rows');

        // Удаляем все колонки
        $this->model->filterIn('row_id', array_keys($rows))->deleteFiltered('layout_cols');

        cmsCache::getInstance()->clean('layout.rows');

        // Удаляем виджеты
        $widgets_bind_pages = $this->model->filterEqual('template', $template_name)->get('widgets_bind_pages');

        if($widgets_bind_pages){

            $this->model->filterEqual('template', $template_name)->deleteFiltered('widgets_bind_pages');

            $bind_ids = [];

            foreach ($widgets_bind_pages as $bind) {
                $bind_ids[] = $bind['bind_id'];
            }

            $this->model->filterIn('id', $bind_ids)->deleteFiltered('widgets_bind');

            cmsCache::getInstance()->clean('widgets.bind_pages');
            cmsCache::getInstance()->clean('widgets.bind');
        }

        return true;
    }

    private function addLayout($to_template_name, $rows, $cols = []) {

        if($cols){

            // Формируем массив колонок для удобной вставки
            $cols_by_rows = [];

            foreach ($cols as $col) {
                $cols_by_rows[$col['row_id']][] = $col;
            }

            $cols_mapping_ids = [];

            foreach ($rows as $row) {

                $old_row_id = $row['id']; unset($row['id']);

                // Указываем новый шаблон
                $row['template'] = $to_template_name;

                $row_id = $this->model->insert('layout_rows', $row);

                if(!empty($cols_by_rows[$old_row_id])){
                    foreach ($cols_by_rows[$old_row_id] as $col_data) {

                        $old_col_id = $col_data['id']; unset($col_data['id']);

                        $col_data['row_id'] = $row_id;

                        $col_id = $this->model->insert('layout_cols', $col_data);

                        $cols_mapping_ids[$old_col_id] = $col_id;
                    }
                }
            }

            // Обновляем id вложенныех рядов
            foreach ($rows as $row) {
                if($row['parent_id']){
                    $this->model->filterEqual('template', $to_template_name);
                    $this->model->filterEqual('parent_id', $row['parent_id']);
                    $this->model->updateFiltered('layout_rows', [
                        'parent_id' => $cols_mapping_ids[$row['parent_id']]
                    ], true);
                }
            }

        } else {
            // Нет колонок, просто добавляем ряды
            foreach ($rows as $row) {
                unset($row['id']);
                $this->model->insert('layout_rows', $row);
            }
        }

        cmsCache::getInstance()->clean('layout.rows');

        return true;
    }

    private function getAvailableWidgets() {

        $widgets = $this->model->get('widgets');
        if (!$widgets) { return []; }

        $sorted = [];

        foreach ($widgets as $widget) {

            $key = $widget['controller'] ? $widget['controller'] : '0';

            $sorted[$key][$widget['name']] = $widget;

        }

        return $sorted;
    }

    private function importFromFile($data, $to_template_name) {

        // Получаем YAML
        $import = cmsModel::yamlToArray(file_get_contents(cmsConfig::get('upload_path').$data['yaml_file']['path']));

        // Удаляем файл
        cmsCore::getModel('files')->deleteFile($data['yaml_file']['id']);

        // небольшая валидация
        if (empty($import['layout']['rows']) ||
                !isset($import['layout']['cols']) ||
                !isset($import['widgets']['widgets_bind_pages']) ||
                !isset($import['widgets']['widgets_bind'])) {
            return [
                'errors' => [
                    'yaml_file' => 'Некорректное содержимое файла'
                ]
            ];
        }

        // Удаляем текущую схему и виджеты
        $this->deleteLayout($to_template_name);

        // Добавляем новую схему
        $this->addLayout($to_template_name, $import['layout']['rows'], $import['layout']['cols']);

        // Расставляем виджеты
        if($import['widgets']['widgets_bind_pages']){

            $widgets = $this->getAvailableWidgets();

            $bind_data = [];

            foreach ($import['widgets']['widgets_bind_pages'] as $bind) {
                $bind_data[$bind['bind_id']][] = $bind;
            }

            foreach ($import['widgets']['widgets_bind'] as $wb) {

                // Получаем ID виджета и проверяем, что такой виджет вообще есть
                $search_key  = $wb['controller'] ? $wb['controller'] : '0';
                $search_name = $wb['name'];

                // Нет такого виджета, пропускаем
                if(!isset($widgets[$search_key][$search_name])){

                    cmsUser::addSessionMessage(sprintf(LANG_CP_WIDGETS_SKIP_IMPORT, $wb['widget_title']), 'info');

                    continue;
                }

                // Заменяем ID виджета
                $wb['widget_id'] = $widgets[$search_key][$search_name]['id'];

                $old_wb_id = $wb['id']; unset($wb['id']);

                $wb['groups_view']      = $wb['groups_view'] ? $wb['groups_view'] : null;
                $wb['groups_hide']      = $wb['groups_hide'] ? $wb['groups_hide'] : null;
                $wb['device_types']     = $wb['device_types'] ? $wb['device_types'] : null;
                $wb['template_layouts'] = $wb['template_layouts'] ? $wb['template_layouts'] : null;
                $wb['languages']        = $wb['languages'] ? $wb['languages'] : null;

                $bind_id = $this->model->insert('widgets_bind', $wb);

                if(!empty($bind_data[$old_wb_id])){
                    foreach ($bind_data[$old_wb_id] as $wbp) {

                        unset($wbp['id']);

                        $wbp['bind_id'] = $bind_id;
                        $wbp['template'] = $to_template_name;

                        $this->model->insert('widgets_bind_pages', $wbp);
                    }
                }
            }

        }

        return true;
    }

    private function copyFromExistsTemplate($data, $to_template_name) {

        // Получаем ряды
        $rows = $this->model->filterEqual('template', $data['from_template'])->get('layout_rows');

        if(!$rows){
            return [
                'errors' => [
                    'from_template' => LANG_CP_WIDGETS_EMPTY_LAYOUT
                ]
            ];
        }

        // Удаляем текущую схему и виджеты
        $this->deleteLayout($to_template_name);

        // Колонки
        $cols_db = $this->model->filterIn('row_id', array_keys($rows))->get('layout_cols');

        $this->addLayout($to_template_name, $rows, $cols_db);

        // Копируем виджеты
        if(!empty($data['copy_widgets'])){

            $widgets_bind_pages = $this->model->filterEqual('template', $data['from_template'])->get('widgets_bind_pages');

            if($widgets_bind_pages){

                $bind_ids = []; $bind_data = [];

                foreach ($widgets_bind_pages as $bind) {
                    $bind_ids[] = $bind['bind_id'];
                    $bind_data[$bind['bind_id']][] = $bind;
                }

                $widgets_bind = $this->model->filterIn('id', $bind_ids)->get('widgets_bind');

                foreach ($widgets_bind as $wb) {

                    $old_wb_id = $wb['id']; unset($wb['id']);

                    $bind_id = $this->model->insert('widgets_bind', $wb);

                    if(!empty($bind_data[$old_wb_id])){
                        foreach ($bind_data[$old_wb_id] as $wbp) {

                            unset($wbp['id']);

                            $wbp['bind_id'] = $bind_id;
                            $wbp['template'] = $to_template_name;

                            $this->model->insert('widgets_bind_pages', $wbp);
                        }
                    }
                }
            }
        }

        return true;
    }

}
