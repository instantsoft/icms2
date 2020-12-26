<?php

class actionAdminWidgetsImportScheme extends cmsAction {

    public function run($to_template_name) {

        $form = $this->getForm('widgets_import_scheme', [$to_template_name]);

        $is_submitted = $this->request->has('from_template');

        $data = $form->parse($this->request, $is_submitted);

        if ($is_submitted) {

            $errors = $form->validate($this, $data);

            if (!$errors) {

                // Получаем ряды
                $rows = $this->model->filterEqual('template', $data['from_template'])->get('layout_rows');

                if(!$rows){
                    return $this->cms_template->renderJSON([
                        'errors' => [
                            'from_template' => LANG_CP_WIDGETS_EMPTY_LAYOUT
                        ]
                    ]);
                }

                // Колонки
                $cols_db = $this->model->filterIn('row_id', array_keys($rows))->get('layout_cols');

                if($cols_db){

                    // Формируем массив колонок для удобной вставки
                    $cols_by_rows = [];

                    foreach ($cols_db as $col) {
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

}
