<?php

class actionAdminCtypesRelationsEdit extends cmsAction {

    public function run($ctype_id, $relation_id){

        if (!$ctype_id || !$relation_id) { cmsCore::error404(); }

        $content_model = cmsCore::getModel('content');

        $ctype = $content_model->getContentType($ctype_id);
        if (!$ctype) { cmsCore::error404(); }

		$relation = $content_model->getContentRelation($relation_id);
        if (!$relation) { cmsCore::error404(); }

        $form = $this->getForm('ctypes_relation', array('edit', $ctype['id']));

        $form->setFieldProperty('basic', 'child_ctype_id', 'is_visible', false);

        if ($relation['layout'] != 'tab'){
            $form->hideFieldset('tab-opts');
        }

        if ($this->request->has('submit')){

            $form->removeField('basic', 'child_ctype_id');

			$relation = array_merge($relation, $form->parse($this->request, true));

            $errors = $form->validate($this,  $relation);

            if($relation['layout'] == 'list' && $content_model->filterEqual('ctype_id', $ctype['id'])->
                    filterEqual('layout', 'list')->filterNotEqual('id', $relation['id'])->
                    getCount('content_relations')){
                $errors['layout'] = LANG_CP_RELATION_LAYOUT_LIST_ERROR;
            }
            $content_model->resetFilters();

            if (!$errors){

                $relation['ctype_id'] = $ctype_id;

                $content_model->updateContentRelation($relation_id, $relation);

                cmsUser::addSessionMessage(LANG_SUCCESS_MSG, 'success');

                $this->redirectToAction('ctypes', array('relations', $ctype['id']));

            }

            if ($errors){
                cmsUser::addSessionMessage(LANG_FORM_ERRORS, 'error');
            }

        }

        $relation['child_ctype_id'] = $relation['target_controller'].':'.$relation['child_ctype_id'];

        return $this->cms_template->render('ctypes_relation', array(
            'do'       => 'edit',
            'ctype'    => $ctype,
            'relation' => $relation,
            'form'     => $form,
            'errors'   => isset($errors) ? $errors : false
        ));

    }

}
