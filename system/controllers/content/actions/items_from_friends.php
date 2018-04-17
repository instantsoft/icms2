<?php

class actionContentItemsFromFriends extends cmsAction {

    public function run(){

        // Получаем название типа контента и сам тип
        $ctype = $this->model->getContentTypeByName($this->request->get('ctype_name', ''));
        if (!$ctype || !$ctype['options']['list_on']) { cmsCore::error404(); }

        if (!$this->cms_user->is_logged){
            cmsUser::goLogin(href_to($ctype['name'], 'from_friends'));
        }

        // Скрываем записи из скрытых родителей (приватных групп и т.п.)
        $this->model->enableHiddenParentsFilter();

        $this->model->filterFriendsPrivateOnly($this->cms_user->id);

		// Получаем HTML списка записей
		$items_list_html = $this->setListContext('items_from_friends')->renderItemsList($ctype, href_to($ctype['name'], 'from_friends'), true);

        return $this->cms_template->render('from_friends', array(
            'ctype'           => $ctype,
            'items_list_html' => $items_list_html,
            'user'            => $this->cms_user
        ), $this->request);

    }

}
