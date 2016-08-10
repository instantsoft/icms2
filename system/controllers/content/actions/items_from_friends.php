<?php

class actionContentItemsFromFriends extends cmsAction {

    public function run(){

        $user = cmsUser::getInstance();

        // Получаем название типа контента и сам тип
        $ctype = $this->model->getContentTypeByName($this->request->get('ctype_name', ''));
        if (!$ctype || !$ctype['options']['list_on']) { cmsCore::error404(); }

        if (!$user->is_logged){
            cmsUser::goLogin(href_to($ctype['name'], 'from_friends'));
        }

        // Скрываем записи из скрытых родителей (приватных групп и т.п.)
        $this->model->filterHiddenParents();

        $this->model->filterFriendsPrivateOnly($user->id);

		// Получаем HTML списка записей
		$items_list_html = $this->renderItemsList($ctype, href_to($ctype['name'], 'from_friends'), true);

        return cmsTemplate::getInstance()->render('from_friends', array(
            'ctype'           => $ctype,
            'items_list_html' => $items_list_html,
            'user'            => $user
        ), $this->request);

    }

}
