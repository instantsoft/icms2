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

                list($target_controller, $child_ctype_id) = explode(':', $relation['child_ctype_id']);

                $relation['child_ctype_id'] = $child_ctype_id ? $child_ctype_id : null;
                $relation['target_controller'] = $target_controller;

                $relation['ctype_id'] = $ctype_id;

                $relation_id = $content_model->addContentRelation($relation);

                if ($relation_id){

                    cmsUser::addSessionMessage(LANG_CP_RELATION_CREATED, 'success');

                    if($relation['target_controller'] != 'content'){

                        $content_model->setTablePrefix('');

                        cmsCore::loadControllerLanguage($relation['target_controller']);

                        $target_ctype = array(
                            'title' => string_lang('LANG_'.strtoupper($relation['target_controller']).'_CONTROLLER'),
                            'name' => $relation['target_controller'],
                            'id'  => null
                        );

                    } else {

                        $target_ctype = $content_model->getContentType($relation['child_ctype_id']);

                    }

                    if (!$content_model->isContentFieldExists($target_ctype['name'], $parent_field_name)){

                        $content_model->addContentField($target_ctype['name'], array(
                            'type'          => 'parent',
                            'ctype_id'      => $target_ctype['id'],
                            'name'          => $parent_field_name,
                            'title'         => string_ucfirst($ctype['labels']['one']),
                            'options'       => array(),
                            'is_fixed'      => true,
                            'is_in_filter'  => false,
                            'is_fixed_type' => true
                        ));

                        if ($content_model->getContentTypeByName($target_ctype['name'])) {
                            cmsUser::addSessionMessage(sprintf(LANG_CP_RELATION_FIELD_CREATED, $target_ctype['title']), 'info');
                        } else {
                            cmsUser::addSessionMessage(sprintf(LANG_CP_CONTR_RELATION_FIELD_CREATED, $target_ctype['title']), 'info');
                        }

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
