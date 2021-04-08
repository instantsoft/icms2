<?php

class actionAdminCtypesPropsReorder extends cmsAction {

    public function run($ctype_name) {

        $items = $this->request->get('items', []);

        if (!$items || !$ctype_name) {
            cmsCore::error404();
        }

        $this->model_backend_content->reorderContentProps($ctype_name, $items);

        if ($this->request->isAjax()) {
            return $this->cms_template->renderJSON([
                'error'        => false,
                'success_text' => LANG_CP_ORDER_SUCCESS
            ]);
        }

        cmsUser::addSessionMessage(LANG_CP_ORDER_SUCCESS, 'success');

        $this->redirectBack();
    }

}
