<?php

class actionAdminWidgetsColDelete extends cmsAction {

    public function run($id){

        $col = $this->model_backend_widgets->getLayoutCol($id);
        if (!$col) { cmsCore::error404(); }

        $row = $this->model_backend_widgets->getLayoutRow($col['row_id']);
        if (!$row) { cmsCore::error404(); }

        $this->model_backend_widgets->
                filterEqual('template', $row['template'])->
                deleteWidgetPageBind($col['name'], 'position');

        $this->model_backend_widgets->deleteLayoutCol($col['id']);

        // если колонок не осталось, удаляем ряд
        $items = $this->model->filterEqual('row_id', $col['row_id'])->get('layout_cols');
        if(!$items){
            $this->model_backend_widgets->deleteLayoutRow($col['row_id']);
        }

        // ищем вложенные ряды
        $ns_items = $this->model_backend_widgets->filterEqual('parent_id', $col['id'])->get('layout_rows');
        if($ns_items){
            foreach ($ns_items as $ns_row) {

                $items = $this->model->filterEqual('row_id', $ns_row['id'])->get('layout_cols');

                if($items){
                    foreach ($items as $item) {
                        $this->model_backend_widgets->
                                filterEqual('template', $ns_row['template'])->
                                deleteWidgetPageBind($item['name'], 'position');
                    }
                }

                $this->model_backend_widgets->deleteLayoutRow($ns_row['id']);
            }
        }

        cmsUser::addSessionMessage(LANG_DELETE_SUCCESS, 'success');

        $this->redirectBack();

    }

}
