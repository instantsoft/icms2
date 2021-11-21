<?php

class actionContentItemTrashPut extends cmsAction {

    public function run(){

        // Получаем название типа контента и сам тип
        $ctype = $this->model->getContentTypeByName($this->request->get('ctype_name', ''));
        if (!$ctype) { cmsCore::error404(); }

        $id = $this->request->get('id', 0);
        if (!$id) { cmsCore::error404(); }

        $item = $this->model->getContentItem($ctype['name'], $id);
        if (!$item || !$item['is_approved']) { cmsCore::error404(); }

        // проверяем наличие доступа
        if (!cmsUser::isAllowed($ctype['name'], 'move_to_trash')) { cmsCore::error404(); }
        if (!cmsUser::isAllowed($ctype['name'], 'move_to_trash', 'all') && $item['user_id'] != $this->cms_user->id) { cmsCore::error404(); }

        // Не вышло ли время для удаления
        if (cmsUser::isPermittedLimitReached($ctype['name'], 'delete_times', ((time() - strtotime($item['date_pub']))/60))){
            cmsUser::addSessionMessage(LANG_CONTENT_PERMS_TIME_UP_DELETE, 'error');
            $this->redirectTo($ctype['name'], $item['slug'] . '.html');
        }

        $back_action = '';

        if ($ctype['is_cats'] && $item['category_id']){

            $category = $this->model->getCategory($ctype['name'], $item['category_id']);
            $back_action = $category['slug'];

        }

        $this->model->toTrashContentItem($ctype['name'], $item);

        $allow_delete = (cmsUser::isAllowed($ctype['name'], 'delete', 'all') ||
            (cmsUser::isAllowed($ctype['name'], 'delete', 'own') && $item['user_id'] == $this->cms_user->id));

        cmsUser::addSessionMessage(($allow_delete ? LANG_BASKET_DELETE_SUCCESS : LANG_DELETE_SUCCESS), 'success');

        // Уведомляем того, кто удаляет о времени жизни в корзине, если она есть
        // общее для всех значение
        $trash_left_time = intval(cmsUser::getPermissionValue($ctype['name'], 'trash_left_time'));

        // если удаляет модератор, проверяем его значение
        $moderator = $this->model->filterEqual('ctype_name', $ctype['name'])->
                filterEqual('user_id', $this->cms_user->id)->
                getItem('moderators');
        if($moderator){
            if($moderator['trash_left_time'] !== null){
                $trash_left_time = intval($moderator['trash_left_time']);
            }
        }

        if((cmsUser::isAllowed($ctype['name'], 'restore') || $moderator) && $trash_left_time){

            cmsUser::addSessionMessage(sprintf(LANG_BASKET_DELETE_LEFT_TIME, html_spellcount($trash_left_time, LANG_HOUR1, LANG_HOUR2, LANG_HOUR10)), 'info');

        }

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
