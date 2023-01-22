<?php
/**
 * @property \modelBackendContent $model_backend_content
 */
class actionAdminCtypesPropsReorder extends cmsAction {

    public function run($ctype_name = null) {

        $items = $this->request->get('items', []);

        if (!$items || !$ctype_name) {
            return cmsCore::error404();
        }

        $ctype = $this->model_backend_content->getContentTypeByName($ctype_name);
        if (!$ctype) {
            return cmsCore::error404();
        }

        $this->model_backend_content->reorderContentProps($ctype['name'], $items);

        if ($this->request->isAjax()) {

            return $this->cms_template->renderJSON([
                'error'        => false,
                'success_text' => LANG_CP_ORDER_SUCCESS
            ]);
        }

        cmsUser::addSessionMessage(LANG_CP_ORDER_SUCCESS, 'success');

        return $this->redirectToAction('ctypes', ['props', $ctype['id']]);
    }

}
