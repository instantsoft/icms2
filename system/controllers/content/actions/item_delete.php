<?php

class actionContentItemDelete extends cmsAction {

    public function run(){

        // Получаем тип контента
        $ctype = $this->model->getContentTypeByName($this->request->get('ctype_name', ''));
        if (!$ctype) { cmsCore::error404(); }

        $item = $this->model->getContentItem($ctype['name'], $this->request->get('id', 0));
        if (!$item) { cmsCore::error404(); }

        // проверяем наличие доступа
        if (!cmsUser::isAllowed($ctype['name'], 'delete')) { cmsCore::error404(); }
        if (!cmsUser::isAllowed($ctype['name'], 'delete', 'all') && $item['user_id'] != $this->cms_user->id) { cmsCore::error404(); }

        $is_moderator = $this->cms_user->is_admin || $this->controller_moderation->model->userIsContentModerator($ctype['name'], $this->cms_user->id);
        if (!$item['is_approved'] && !$is_moderator && !$item['is_draft']) { cmsCore::error404(); }

        // в случае отклонения неодобренной записи
        if ($this->request->isAjax() && !$item['is_approved'] && !$item['is_draft']){

            return $this->cms_template->render('item_refuse', array(
                'ctype' => $ctype,
                'item'  => $item
            ));

        }

        if (!cmsForm::validateCSRFToken($this->request->get('csrf_token', ''))){ cmsCore::error404(); }

        $back_action = '';

        if ($ctype['is_cats'] && $item['category_id']){

            $category = $this->model->getCategory($ctype['name'], $item['category_id']);
            $back_action = $category['slug'];

        }

        $this->model->deleteContentItem($ctype['name'], $item['id']);

        if (!$item['is_approved'] && !$item['is_draft'] && $item['user_id'] != $this->cms_user->id){

            $item['reason'] = trim(strip_tags($this->request->get('reason', '')));

            $this->controller_moderation->moderationNotifyAuthor($item, 'moderation_refused');

        }

        cmsUser::addSessionMessage(LANG_DELETE_SUCCESS, 'success');

        $back_url = $this->request->get('back', '');

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
