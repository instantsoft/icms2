<?php

class actionAdminCtypesRelationsReorder extends cmsAction {

    public function run() {

        $items = $this->request->get('items', []);
        if (!$items) { cmsCore::error404(); }

        $this->model_backend_content->reorderContentRelation($items);

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
