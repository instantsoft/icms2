<?php
/**
 * @property \modelBackendContent $model_backend_content
 */
class actionAdminCtypesRelationsAdd extends cmsAction {

    public function run($ctype_id = null) {

        if (!$ctype_id) {
            return cmsCore::error404();
        }

        $ctype = $this->model_backend_content->getContentType($ctype_id);
        if (!$ctype) {
            return cmsCore::error404();
        }

        $form = $this->getForm('ctypes_relation', ['add', $ctype['id']]);

        $relation = [];

        if ($this->request->has('submit')) {

            $relation = $form->parse($this->request, true);

            $errors = $form->validate($this, $relation);

            $parent_field_name = "parent_{$ctype['name']}_id";

            if (mb_strlen($parent_field_name) > 40) {
                $errors['child_ctype_id'] = LANG_CP_RELATION_ERROR_LEN;
            }

            if ($relation['layout'] == 'list' && $this->model_backend_content->filterEqual('ctype_id', $ctype['id'])->
                            filterEqual('layout', 'list')->
                            getCount('content_relations')) {
                $errors['layout'] = LANG_CP_RELATION_LAYOUT_LIST_ERROR;
            }
            $this->model_backend_content->resetFilters();

            if (!$errors) {

                list($target_controller, $child_ctype_id) = explode(':', $relation['child_ctype_id']);

                $relation['child_ctype_id']    = $child_ctype_id ? $child_ctype_id : null;
                $relation['target_controller'] = $target_controller;

                $relation['ctype_id'] = $ctype_id;

                $relation_id = $this->model_backend_content->addContentRelation($relation);

                if ($relation_id) {

                    cmsUser::addSessionMessage(LANG_CP_RELATION_CREATED, 'success');

                    if ($relation['target_controller'] !== 'content') {

                        $this->model_backend_content->setTablePrefix('');

                        cmsCore::loadControllerLanguage($relation['target_controller']);

                        $target_ctype = [
                            'title' => string_lang('LANG_' . strtoupper($relation['target_controller']) . '_CONTROLLER'),
                            'name'  => $relation['target_controller'],
                            'id'    => null
                        ];

                    } else {
                        $target_ctype = $this->model_backend_content->getContentType($relation['child_ctype_id']);
                    }

                    if (!$this->model_backend_content->isContentFieldExists($target_ctype['name'], $parent_field_name)) {

                        $this->model_backend_content->addContentField($target_ctype['name'], [
                            'type'          => 'parent',
                            'ctype_id'      => $target_ctype['id'],
                            'name'          => $parent_field_name,
                            'title'         => string_ucfirst($ctype['labels']['one']),
                            'options'       => [],
                            'is_fixed'      => true,
                            'is_in_filter'  => false,
                            'is_fixed_type' => true
                        ]);

                        if ($this->model_backend_content->getContentTypeByName($target_ctype['name'])) {
                            cmsUser::addSessionMessage(sprintf(LANG_CP_RELATION_FIELD_CREATED, $target_ctype['title']), 'info');
                        } else {
                            cmsUser::addSessionMessage(sprintf(LANG_CP_CONTR_RELATION_FIELD_CREATED, $target_ctype['title']), 'info');
                        }
                    }
                }

                return $this->redirectToAction('ctypes', ['relations', $ctype['id']]);
            }

            if ($errors) {
                cmsUser::addSessionMessage(LANG_FORM_ERRORS, 'error');
            }
        }

        // Для того, чтобы сформировалось подменю типа контента, см system/controllers/admin/actions/ctypes.php
        $this->dispatchEvent('ctype_loaded', [$ctype, 'relations']);

        return $this->cms_template->render('ctypes_relation', [
            'do'       => 'add',
            'ctype'    => $ctype,
            'relation' => $relation,
            'form'     => $form,
            'errors'   => isset($errors) ? $errors : false
        ]);
    }

}
