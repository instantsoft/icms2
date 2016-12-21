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

        $form->removeField('basic', 'child_ctype_id');

        if ($relation['layout'] != 'tab'){
            $form->hideFieldset('tab-opts');
        }

        if ($this->request->has('submit')){

			$relation = array_merge($relation, $form->parse($this->request, true));

            $errors = $form->validate($this,  $relation);

            if (!$errors){

                $relation['ctype_id'] = $ctype_id;

                $content_model->updateContentRelation($relation_id, $relation);

                $this->redirectToAction('ctypes', array('relations', $ctype['id']));

            }

            if ($errors){
                cmsUser::addSessionMessage(LANG_FORM_ERRORS, 'error');
            }

        }

        return $this->cms_template->render('ctypes_relation', array(
            'do' => 'edit',
            'ctype' => $ctype,
            'relation' => $relation,
            'form' => $form,
            'errors' => isset($errors) ? $errors : false
        ));

    }

}
