<?php

class actionAdminWidgetsExportScheme extends cmsAction {

    public function run($from_template) {

        $this->model->localizedOff();

        $widgets = $this->getExistsWidgets($from_template);

        $form = $this->getForm('widgets_export_scheme', [$widgets]);

        $is_submitted = $this->request->has('submit');

        $data = $form->parse($this->request, $is_submitted);

        // Получаем ряды
        $rows = $this->model->filterEqual('template', $from_template)->get('layout_rows');

        if(!$rows){
            return $this->cms_template->render([
                'is_rows' => false,
                'from_template' => $from_template,
                'data'   => $data,
                'form'   => $form,
                'errors' => false
            ]);
        }

        if ($is_submitted) {

            $errors = $form->validate($this, $data);

            if (!$errors) {

                // Колонки
                $cols = $this->model->filterIn('row_id', array_keys($rows))->get('layout_cols');

                $export = [
                    'layout' => [
                        'rows' => $rows,
                        'cols' => $cols
                    ],
                    'widgets' => [
                        'widgets_bind_pages' => [],
                        'widgets_bind' => []
                    ]
                ];

                // Сохраняем виджеты
                if(!empty($data['save_widgets'])){

                    if($data['save_widgets_list']){
                        $this->model->filterIn('id', $data['save_widgets_list']);
                    }

                    $widgets_bind_pages = $this->model->filterEqual('template', $from_template)->get('widgets_bind_pages');

                    if($widgets_bind_pages){

                        $export['widgets']['widgets_bind_pages'] = $widgets_bind_pages;

                        $bind_ids = [];

                        foreach ($widgets_bind_pages as $bind) {
                            $bind_ids[] = $bind['bind_id'];
                        }

                        $this->model->select('w.controller', 'controller');
                        $this->model->select('w.name', 'name');
                        $this->model->select('w.title', 'widget_title');
                        $this->model->join('widgets', 'w', 'w.id = i.widget_id');

                        $export['widgets']['widgets_bind'] = $this->model->filterIn('id', $bind_ids)->get('widgets_bind', function($item, $model){
                            $item['options']          = cmsModel::yamlToArray($item['options']);
                            $item['groups_view']      = cmsModel::yamlToArray($item['groups_view']);
                            $item['groups_hide']      = cmsModel::yamlToArray($item['groups_hide']);
                            $item['device_types']     = cmsModel::yamlToArray($item['device_types']);
                            $item['template_layouts'] = cmsModel::yamlToArray($item['template_layouts']);
                            $item['languages']        = cmsModel::yamlToArray($item['languages']);
                            return $item;
                        });
                    }
                }

                return $this->cms_template->renderJSON([
                    'errors'   => false,
                    'filename' => $from_template.' - InstantCMS Widgets Scheme.yaml',
                    'yaml'     => cmsModel::arrayToYaml($export),
                    'callback' => 'successExport'
                ]);
            }

            if ($errors) {
                return $this->cms_template->renderJSON([
                    'errors' => $errors
                ]);
            }
        }

        return $this->cms_template->render([
            'is_rows' => true,
            'from_template' => $from_template,
            'data'   => $data,
            'form'   => $form,
            'errors' => false
        ]);
    }

    private function getExistsWidgets($from_template) {

        $this->model->join('widgets_bind', 'b', 'b.id = i.bind_id');

        $this->model->selectOnly('i.id');
        $this->model->select('b.title', 'title');

        return $this->model->filterEqual('template', $from_template)->get('widgets_bind_pages', function($item, $model){
            return $item['title'];
        }) ?: [];
      }

}
