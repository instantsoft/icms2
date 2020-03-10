<?php

class actionAdminCtypesFiltersAdd extends cmsAction {

    public function run($ctype_id, $id = null, $do = 'add'){

        if (!$ctype_id) { cmsCore::error404(); }

        $ctype = $this->model_content->getContentType($ctype_id);
        if (!$ctype) { cmsCore::error404(); }

        $fields  = $this->model_content->getContentFields($ctype['name']);
        $fields = cmsEventsManager::hook('ctype_content_fields', $fields);

        $props = $this->model_content->getContentProps($ctype['name']);
        $props_fields = cmsCore::getController('content')->getPropsFields($props);

        $filter = [];

        if($id){
            $filter = $this->model_content->getContentFilter($ctype, $id);
            if (!$filter) { cmsCore::error404(); }
        }

        $table_name = $this->model_content->table_prefix . $ctype['name'] . '_filters';

        $form = $this->getForm('ctypes_filter', array($do, $ctype, $fields, $props_fields, $table_name, $filter));

        if ($this->request->has('submit')){

			$filter = array_replace_recursive($filter, $form->parse($this->request, true));

            $errors = $form->validate($this,  $filter);

            $category = $this->model_content->getCategoryBySLUG($ctype['name'], $filter['slug']);

            if($category){
                $errors['slug'] = LANG_CP_FILTER_ERROR_SLUG;
            }

            if (!$errors){

                if($do == 'add'){

                    $this->model_content->addContentFilter($filter, $ctype);

                } else {

                    $this->model_content->updateContentFilter($filter, $ctype);

                }

                cmsUser::addSessionMessage(LANG_SUCCESS_MSG, 'success');

                $this->redirectToAction('ctypes', array('filters', $ctype['id']));

            }

            if ($errors){
                cmsUser::addSessionMessage(LANG_FORM_ERRORS, 'error');
            }

        }

        return $this->cms_template->render('ctypes_filter', array(
            'do'      => $do,
            'ctype'   => $ctype,
            'filter'  => $filter,
            'form'    => $form,
            'errors'  => isset($errors) ? $errors : false
        ));

    }

}
