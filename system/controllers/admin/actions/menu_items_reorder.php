<?php

class actionAdminMenuItemsReorder extends cmsAction {

    public function run(){

        $items = $this->request->get('items', array());
        if (!$items){ cmsCore::error404(); }

        cmsCore::getModel('menu')->reorderMenuItems($items);

        if ($this->request->isAjax()){
			return $this->cms_template->renderJSON(array(
				'error' => false,
				'success_text' => LANG_CP_ORDER_SUCCESS
			));
        }

        cmsUser::addSessionMessage(LANG_CP_ORDER_SUCCESS, 'success');

        $this->redirectBack();

    }

}
