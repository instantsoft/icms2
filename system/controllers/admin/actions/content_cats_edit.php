<?php
/**
 * @property \modelContent $model_backend_content
 */
class actionAdminContentCatsEdit extends cmsAction {

    public function run($ctype_id = false, $category_id = false) {

        if (!$ctype_id) {
            return $this->redirectBack();
        }
        if (!$category_id) {
            return cmsCore::error404();
        }

        $ctype = $this->model_backend_content->getContentType($ctype_id);
        if (!$ctype) {
            return cmsCore::error404();
        }

        $this->cms_template->addBreadcrumb(LANG_CP_SECTION_CONTENT, $this->cms_template->href_to('content'));
        $this->cms_template->addBreadcrumb($ctype['title'], $this->cms_template->href_to('content', [$ctype['id']]));

        $this->cms_core->uri_controller = 'content';
        $this->cms_core->controller     = 'content';
        $this->cms_core->uri_action     = $ctype['name'];
        $this->cms_core->uri            = $ctype['name'] . '/editcat/' . $category_id;

        $controller = cmsCore::getController('content', $this->request);

        $controller->request->set('ctype_name', $ctype['name']);
        $controller->request->set('id', $category_id);
        $controller->request->set('back', $this->getRequestBackUrl(href_to($this->name, 'content')));

        $controller->executeAction('category_edit', []);
    }

}
