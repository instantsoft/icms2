<?php
/**
 * @property \modelContent $model
 */
class actionContentCategoryDelete extends cmsAction {

    public function run() {

        // Получаем название типа контента и сам тип
        $ctype = $this->model->getContentTypeByName($this->request->get('ctype_name', ''));
        if (!$ctype) {
            return cmsCore::error404();
        }

        // проверяем наличие доступа
        if (!cmsUser::isAllowed($ctype['name'], 'delete_cat')) {
            return cmsCore::error404();
        }

        $category = $this->model->getCategory($ctype['name'], $this->request->get('id', 0));
        if (!$category) {
            return cmsCore::error404();
        }

        if ($category['path'] && count($category['path']) > 1) {
            $path   = array_values($category['path']);
            $parent = $path[count($category['path']) - 2];
        }

        $this->model->deleteCategory($ctype['name'], $category['id'], true);

        list($ctype, $category) = cmsEventsManager::hook('content_category_after_delete', [$ctype, $category], null, $this->request);
        list($ctype, $category) = cmsEventsManager::hook("content_{$ctype['name']}_category_after_delete", [$ctype, $category], null, $this->request);

        $back_url = $this->getRequestBackUrl();

        if ($back_url) {

            $this->redirect($back_url);

        } else {

            if ($ctype['options']['list_on']) {
                if (isset($parent)) {
                    $this->redirectTo($ctype['name'], $parent['slug']);
                } else {
                    $this->redirectTo($ctype['name']);
                }
            } else {
                $this->redirectToHome();
            }
        }
    }

}
