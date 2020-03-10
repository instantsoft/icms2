<?php

class actionAdminCtypesFieldsReorder extends cmsAction {

    public function run($ctype_name){

        $items = $this->request->get('items', array());

        if (!$items || !$ctype_name){ cmsCore::error404(); }

        cmsCore::getModel('content')->reorderContentFields($ctype_name, $items);

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
