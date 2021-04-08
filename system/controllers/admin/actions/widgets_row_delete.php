<?php

class actionAdminWidgetsRowDelete extends cmsAction {

    public function run($id){

        $row = $this->model_backend_widgets->getLayoutRow($id);
        if (!$row) { cmsCore::error404(); }

        $this->deleteRow($row);

        cmsUser::addSessionMessage(LANG_DELETE_SUCCESS, 'success');

        $this->redirectBack();

    }

    private function deleteRow($row) {

        $items = $this->model->filterEqual('row_id', $row['id'])->get('layout_cols');

        if($items){
            foreach ($items as $item) {

                $this->model_backend_widgets->filterEqual('template', $row['template'])->
                        deleteWidgetPageBind($item['name'], 'position');

                // ищем вложенные ряды
                $ns_items = $this->model_backend_widgets->filterEqual('parent_id', $item['id'])->get('layout_rows');
                if($ns_items){
                    foreach ($ns_items as $ns_row) {
                        $this->deleteRow($ns_row);
}
                }

            }
        }

        $this->model_backend_widgets->deleteLayoutRow($row['id']);

    }

}
