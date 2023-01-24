<?php
/**
 * @property \modelBackendContent $model_backend_content
 */
class actionAdminCtypesFiltersAdd extends cmsAction {

    public function run($ctype_id = null, $id = null, $do = 'add') {

        if (!$ctype_id) {
            return cmsCore::error404();
        }

        $ctype = $this->model_backend_content->getContentType($ctype_id);
        if (!$ctype) {
            return cmsCore::error404();
        }

        $this->model_backend_content->loadAllCtypes();

        $this->dispatchEvent('ctype_loaded', [$ctype, 'filters']);

        $fields = $this->model_backend_content->getContentFields($ctype['name']);
        $fields = cmsEventsManager::hook('ctype_content_fields', $fields);

        $props = $this->model_backend_content->getContentProps($ctype['name']);
        $props_fields = cmsCore::getController('content')->getPropsFields($props);

        $filter = [
            'ctype_name' => $ctype['name']
        ];

        if ($id) {

            $filter = $this->model_backend_content->localizedOff()->getContentFilter($ctype, $id);
            if (!$filter) {
                return cmsCore::error404();
            }

            $this->model_backend_content->localizedRestore();
        }

        $table_name = $this->model_backend_content->table_prefix . $ctype['name'] . '_filters';

        $form = $this->getForm('ctypes_filter', [$do, $ctype, $fields, $props_fields, $table_name, $filter]);

        if ($this->request->has('submit')) {

            $filter['filters'] = [];

            $filter = array_replace_recursive($filter, $form->parse($this->request, true));

            $errors = $form->validate($this, $filter);

            $category = $this->model_backend_content->getCategoryBySLUG($ctype['name'], $filter['slug']);

            if ($category) {
                $errors['slug'] = LANG_CP_FILTER_ERROR_SLUG;
            }

            if (!$errors) {

                if ($do === 'add') {

                    $this->model_backend_content->addContentFilter($filter, $ctype);

                } else {

                    $this->model_backend_content->updateContentFilter($filter, $ctype);

                }

                cmsUser::addSessionMessage(LANG_SUCCESS_MSG, 'success');

                return $this->redirectToAction('ctypes', ['filters', $ctype['id']]);
            }

            if ($errors) {
                cmsUser::addSessionMessage(LANG_FORM_ERRORS, 'error');
            }
        }

        return $this->cms_template->render('ctypes_filter', [
            'do'     => $do,
            'ctype'  => $ctype,
            'filter' => $filter,
            'form'   => $form,
            'errors' => isset($errors) ? $errors : false
        ]);
    }

}
