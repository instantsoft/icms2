<?php
/**
 * @deprecated Используется в шаблоне старой админки
 * href_to('admin', 'reorder', ['content_types'])
 */
class actionAdminCtypesReorder extends cmsAction {

    public function run() {

        $items = $this->request->get('items', []);
        if (!$items) { cmsCore::error404(); }

        $this->model_backend_content->reorderContentTypes($items);

        cmsUser::addSessionMessage(LANG_CP_ORDER_SUCCESS, 'success');

        $this->redirectBack();
    }

}
