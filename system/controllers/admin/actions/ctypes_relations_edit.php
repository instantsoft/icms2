<?php
/**
 * @property \modelBackendContent $model_backend_content
 */
class actionAdminCtypesRelationsEdit extends cmsAction {

    public function run($ctype_id = null, $relation_id = null) {

        if (!$ctype_id || !$relation_id) {
            return cmsCore::error404();
        }

        $ctype = $this->model_backend_content->getContentType($ctype_id);
        if (!$ctype) {
            return cmsCore::error404();
        }

        $relation = $this->model_backend_content->localizedOff()->getContentRelation($relation_id);
        if (!$relation) {
            return cmsCore::error404();
        }

        $this->model_backend_content->localizedRestore();

        $form = $this->getForm('ctypes_relation', ['edit', $ctype['id']]);

        $form->setFieldProperty('basic', 'child_ctype_id', 'is_visible', false);

        if ($relation['layout'] !== 'tab') {
            $form->hideFieldset('tab-opts');
        }

        if ($this->request->has('submit')) {

            $form->removeField('basic', 'child_ctype_id');

            $relation = array_merge($relation, $form->parse($this->request, true));

            $errors = $form->validate($this, $relation);

            if ($relation['layout'] == 'list' && $this->model_backend_content->filterEqual('ctype_id', $ctype['id'])->
                            filterEqual('layout', 'list')->filterNotEqual('id', $relation['id'])->
                            getCount('content_relations')) {
                $errors['layout'] = LANG_CP_RELATION_LAYOUT_LIST_ERROR;
            }
            $this->model_backend_content->resetFilters();

            if (!$errors) {

                $relation['ctype_id'] = $ctype_id;

                $this->model_backend_content->updateContentRelation($relation_id, $relation);

                cmsUser::addSessionMessage(LANG_SUCCESS_MSG, 'success');

                return $this->redirectToAction('ctypes', ['relations', $ctype['id']]);
            }

            if ($errors) {
                cmsUser::addSessionMessage(LANG_FORM_ERRORS, 'error');
            }
        }

        $relation['child_ctype_id'] = $relation['target_controller'] . ':' . $relation['child_ctype_id'];

        // Для того, чтобы сформировалось подменю типа контента, см system/controllers/admin/actions/ctypes.php
        $this->dispatchEvent('ctype_loaded', [$ctype, 'relations']);

        return $this->cms_template->render('ctypes_relation', [
            'do'       => 'edit',
            'ctype'    => $ctype,
            'relation' => $relation,
            'form'     => $form,
            'errors'   => isset($errors) ? $errors : false
        ]);
    }

}
