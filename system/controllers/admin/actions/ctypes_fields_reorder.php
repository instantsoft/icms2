<?php
/**
 * @property \modelContent $model_content
 */
class actionAdminCtypesFieldsReorder extends cmsAction {

    public function run($ctype_name = null) {

        $ctype = $this->model_content->getContentTypeByName($ctype_name);
        if (!$ctype) {
            return cmsCore::error404();
        }

        $items = $this->request->get('items', []);

        if (!$items) {
            return cmsCore::error404();
        }

        $this->model_content->reorderContentFields($ctype['name'], $items);

        if ($this->request->isAjax()) {

            return $this->cms_template->renderJSON([
                'error'        => false,
                'success_text' => LANG_CP_ORDER_SUCCESS
            ]);
        }

        cmsUser::addSessionMessage(LANG_CP_ORDER_SUCCESS, 'success');

        return $this->redirectToAction('ctypes', ['fields', $ctype['id']]);
    }

}
