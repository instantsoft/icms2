<?php
/**
 * @property \modelBackendContent $model_backend_content
 */
class actionAdminCtypesDatasetsEdit extends cmsAction {

    public function run($dataset_id = null) {

        if (!$dataset_id) {
            return cmsCore::error404();
        }

        $dataset = $old_dataset = $this->model_backend_content->localizedOff()->getContentDataset($dataset_id);

        if (!$dataset) {
            return cmsCore::error404();
        }

        $this->model_backend_content->localizedRestore();

        if ($dataset['ctype_id']) {

            $ctype = $this->model_backend_content->getContentType($dataset['ctype_id']);
            if (!$ctype) {
                return cmsCore::error404();
            }

            $controller_name = 'content';

            $this->dispatchEvent('ctype_loaded', [$ctype, 'datasets']);

        } else {

            cmsCore::loadControllerLanguage($dataset['target_controller']);

            $ctype = [
                'title' => string_lang($dataset['target_controller'] . '_controller'),
                'name'  => $dataset['target_controller'],
                'id'    => null
            ];

            $this->model_backend_content->setTablePrefix('');

            $controller_name = $dataset['target_controller'];
        }

        $fields = $this->model_backend_content->getContentFields($ctype['name']);
        $fields = cmsEventsManager::hook('ctype_content_fields', $fields);

        $cats_list = [];

        if ($ctype['id']) {

            $cats = $this->model_backend_content->getCategoriesTree($ctype['name'], false);

            if ($cats) {
                foreach ($cats as $c) {
                    $cats_list[$c['id']] = str_repeat('-- ', $c['ns_level'] - 1) . ' ' . $c['title'];
                }
            }
        }

        $fields_list = $this->buildDatasetFieldsList($controller_name, $fields);

        $form = $this->getForm('ctypes_dataset', ['edit', $ctype, $cats_list, $fields_list, $dataset]);

        if ($this->request->has('submit')) {

            $dataset = $form->parse($this->request, true);

            $errors = $form->validate($this, $dataset);

            if (!$errors) {

                $this->model_backend_content->updateContentDataset($dataset_id, $dataset, $ctype, $old_dataset);

                cmsUser::addSessionMessage(LANG_CP_SAVE_SUCCESS, 'success');

                if ($ctype['id']) {
                    return $this->redirectToAction('ctypes', ['datasets', $ctype['id']]);
                }

                return $this->redirect(href_to('admin', 'controllers', ['edit', $ctype['name'], 'datasets']));
            }

            if ($errors) {
                cmsUser::addSessionMessage(LANG_FORM_ERRORS, 'error');
            }
        }

        return $this->cms_template->render('ctypes_dataset', [
            'do'      => 'edit',
            'ctype'   => $ctype,
            'dataset' => $dataset,
            'form'    => $form,
            'errors'  => isset($errors) ? $errors : false
        ]);
    }

}
