<?php

class actionAdminCtypesRelationsAdd extends cmsAction {

    public function run($ctype_id){

        if (!$ctype_id) { cmsCore::error404(); }

        $content_model = cmsCore::getModel('content');

        $ctype = $content_model->getContentType($ctype_id);
        if (!$ctype) { cmsCore::error404(); }

        $form = $this->getForm('ctypes_relation', array('add', $ctype['id']));

		$relation = array();

        if ($this->request->has('submit')){

			$relation = $form->parse($this->request, true);

            $errors = $form->validate($this,  $relation);

            $parent_field_name = "parent_{$ctype['name']}_id";

            if (mb_strlen($parent_field_name) > 40){
                $errors['child_ctype_id'] = LANG_CP_RELATION_ERROR_LEN;
            }

            if($relation['layout'] == 'list' && $content_model->filterEqual('ctype_id', $ctype['id'])->
                    filterEqual('layout', 'list')->
                    getCount('content_relations')){
                $errors['layout'] = LANG_CP_RELATION_LAYOUT_LIST_ERROR;
            }
            $content_model->resetFilters();

            if (!$errors){

                $relation['ctype_id'] = $ctype_id;

                $relation_id = $content_model->addContentRelation($relation);

                if ($relation_id){

                    cmsUser::addSessionMessage(LANG_CP_RELATION_CREATED, 'success');

                    $target_ctype = $content_model->getContentType($relation['child_ctype_id']);

                    if (!$content_model->isContentFieldExists($target_ctype['name'], $parent_field_name)){

                        $content_model->addContentField($target_ctype['name'], array(
                            'type'          => 'parent',
                            'ctype_id'      => $target_ctype['id'],
                            'name'          => $parent_field_name,
                            'title'         => mb_convert_case($ctype['labels']['one'], MB_CASE_TITLE),
                            'options'       => array(),
                            'is_fixed'      => true,
                            'is_in_filter'  => false,
                            'is_fixed_type' => true
                        ));

                        cmsUser::addSessionMessage(sprintf(LANG_CP_RELATION_FIELD_CREATED, $target_ctype['title']), 'success');

                    }

                }

                $this->redirectToAction('ctypes', array('relations', $ctype['id']));

            }

            if ($errors){
                cmsUser::addSessionMessage(LANG_FORM_ERRORS, 'error');
            }

        }

        return $this->cms_template->render('ctypes_relation', array(
            'do'       => 'add',
            'ctype'    => $ctype,
            'relation' => $relation,
            'form'     => $form,
            'errors'   => isset($errors) ? $errors : false
        ));

    }

}
