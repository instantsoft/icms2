<?php
/**
 * @property \modelContent $model_backend_content
 */
class actionAdminContentItemEdit extends cmsAction {

    public function run($ctype_name, $id) {

        $ctype = $this->model_backend_content->getContentTypeByName($ctype_name);
        if (!$ctype) {
            return cmsCore::error404();
        }

        $this->cms_template->addBreadcrumb(LANG_CP_SECTION_CONTENT, $this->cms_template->href_to('content'));
        $this->cms_template->addBreadcrumb($ctype['title'], $this->cms_template->href_to('content', [$ctype['id']]));

        $this->cms_core->uri_controller = 'content';
        $this->cms_core->controller     = 'content';
        $this->cms_core->uri_action     = $ctype['name'];
        $this->cms_core->uri            = $ctype['name'] . '/edit/' . $id;

        $controller = cmsCore::getController('content', $this->request);

        $controller->request->set('ctype_name', $ctype['name']);
        $controller->request->set('id', $id);

        $controller->executeAction('item_edit', []);
    }

}
