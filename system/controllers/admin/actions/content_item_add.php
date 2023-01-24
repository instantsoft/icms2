<?php
/**
 * @property \modelBackendContent $model_backend_content
 */
class actionAdminContentItemAdd extends cmsAction {

    public function run($ctype_id, $category_id = 1, $add_from_admin = 0) {

        $ctype = $this->model_backend_content->getContentType($ctype_id);
        if (!$ctype) {
            return cmsCore::error404();
        }

        if ($add_from_admin) {

            $this->cms_template->addBreadcrumb(LANG_CP_SECTION_CONTENT, $this->cms_template->href_to('content'));
            $this->cms_template->addBreadcrumb($ctype['title'], $this->cms_template->href_to('content', [$ctype['id']]));

            $this->cms_core->uri_controller = 'content';
            $this->cms_core->controller     = 'content';
            $this->cms_core->uri_action     = $ctype['name'];
            $this->cms_core->uri            = $ctype['name'] . '/add' . ($category_id ? '/' . $category_id : '');

            $controller = cmsCore::getController('content', $this->request);

            $controller->request->set('ctype_name', $ctype['name']);
            $controller->request->set('to_id', $category_id);
            $controller->request->set('back', href_to($this->name, 'content'));

            $controller->executeAction('item_add', []);

        } else {

            $params = $category_id > 1 ? [$category_id] : false;

            $url = href_to($ctype['name'], 'add', $params) . '?back=' . href_to($this->name, 'content');

            $this->redirect($url);
        }
    }

}
