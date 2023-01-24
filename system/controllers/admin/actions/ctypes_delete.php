<?php
/**
 * @property \modelBackendContent $model_backend_content
 */
class actionAdminCtypesDelete extends cmsAction {

    public function run($id = null) {

        if (!$id) {
            return cmsCore::error404();
        }

        if (!cmsForm::validateCSRFToken($this->request->get('csrf_token', ''))) {
            return cmsCore::error404();
        }

        $ctype = $this->model_backend_content->getContentType($id);

        if (!$ctype) {
            return cmsCore::error404();
        }

        $ctype = cmsEventsManager::hook('ctype_before_delete', $ctype);

        $this->model_backend_content->deleteContentType($id);

        cmsEventsManager::hook('ctype_after_delete', $ctype);

        $this->model_backend_widgets->deletePagesByName('content', "{$ctype['name']}.*");

        $binded_widgets = $this->model_backend_content->get('widgets_bind', function ($item, $model) {
            $item['options'] = cmsModel::yamlToArray($item['options']);
            return $item;
        });

        if ($binded_widgets) {
            foreach ($binded_widgets as $widget) {
                if (isset($widget['options']['ctype_id']) && $ctype['id'] == $widget['options']['ctype_id']) {
                    $this->model_backend_content->delete('widgets_bind', $widget['id']);
                }
            }
        }

        cmsUser::addSessionMessage(LANG_DELETE_SUCCESS, 'success');

        return $this->redirectToAction('ctypes');
    }

}
