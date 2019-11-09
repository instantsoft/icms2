<?php
/**
 * @deprecated Используется в шаблоне старой админки
 * по новому сохранение тут href_to('admin', 'reorder', ['events'])
 */
class actionAdminControllersEventsReorder extends cmsAction {

    public function run(){

        $items = $this->request->get('items', array());
        if (!$items){ cmsCore::error404(); }

        $this->model->reorderEvents($items);

        cmsUser::addSessionMessage(LANG_CP_ORDER_SUCCESS, 'success');

        $this->redirectBack();

    }

}
