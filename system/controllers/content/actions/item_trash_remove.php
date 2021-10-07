<?php

class actionContentItemTrashRemove extends cmsAction {

    public function run(){

        // Получаем название типа контента и сам тип
        $ctype = $this->model->getContentTypeByName($this->request->get('ctype_name', ''));
        if (!$ctype) { cmsCore::error404(); }

        $id = $this->request->get('id', 0);
        if (!$id) { cmsCore::error404(); }

        $item = $this->model->getContentItem($ctype['name'], $id);
        if (!$item || !$item['is_approved']) { cmsCore::error404(); }

        // проверяем наличие доступа
        if (!cmsUser::isAllowed($ctype['name'], 'restore')) { cmsCore::error404(); }
        if (!cmsUser::isAllowed($ctype['name'], 'restore', 'all') && $item['user_id'] != $this->cms_user->id) { cmsCore::error404(); }

        $back_action = '';

        if ($ctype['is_cats'] && $item['category_id']){

            $category = $this->model->getCategory($ctype['name'], $item['category_id']);
            $back_action = $category['slug'];

        }

        $this->model->restoreContentItem($ctype['name'], $item);

        cmsUser::addSessionMessage(LANG_ITEM_RESTORE_SUCCESS, 'success');

        $back_url = $this->getRequestBackUrl();

        if ($back_url){
            $this->redirect($back_url);
        } else {
            if ($ctype['options']['list_on']){
                $this->redirectTo($ctype['name'], $back_action);
            } else {
                $this->redirectToHome();
            }
        }

    }

}
