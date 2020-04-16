<?php

class actionAdminCtypesDatasetsAdd extends cmsAction {

    public function run($ctype_id){

        if (!$ctype_id) { cmsCore::error404(); }

        if(is_numeric($ctype_id)){

            $ctype = $this->model_content->getContentType($ctype_id);
            if (!$ctype) { cmsCore::error404(); }

            $controller_name = 'content';

        } else {

            if(!$this->isControllerInstalled($ctype_id)){
                cmsCore::error404();
            }

            cmsCore::loadControllerLanguage($ctype_id);

            $ctype = array(
                'title' => string_lang($ctype_id.'_controller'),
                'name'  => $ctype_id,
                'id'    => null
            );

            $this->model_content->setTablePrefix('');

            $controller_name = $ctype_id;

        }

        $fields  = $this->model_content->getContentFields($ctype['name']);
        $fields = cmsEventsManager::hook('ctype_content_fields', $fields);

        $cats_list = array();

        if($ctype['id']){

            $cats = $this->model_content->getCategoriesTree($ctype['name'], false);

            if ($cats){
                foreach($cats as $c){
                    $cats_list[$c['id']] = str_repeat('-- ', $c['ns_level']-1).' '.$c['title'];
                }
            }

        }

        $fields_list = $this->buildDatasetFieldsList($controller_name, $fields);

        $form = $this->getForm('ctypes_dataset', array('add', $ctype, $cats_list, $fields_list));

        if ($this->request->has('submit')){

			$dataset = $form->parse($this->request, true);

            $errors = $form->validate($this,  $dataset);

            if (!$errors){

                if(!$ctype['id']){
                    $dataset['target_controller'] = $controller_name;
                }

                $dataset_id = $this->model_content->addContentDataset($dataset, $ctype);

                if ($dataset_id){ cmsUser::addSessionMessage(sprintf(LANG_CP_DATASET_CREATED, $dataset['title']), 'success'); }

                if($ctype['id']){
                    $this->redirectToAction('ctypes', array('datasets', $ctype['id']));
                }

                $this->redirect(href_to('admin', 'controllers', array('edit', $ctype['name'], 'datasets')));

            }

            if ($errors){
                cmsUser::addSessionMessage(LANG_FORM_ERRORS, 'error');
            }

        }

        return $this->cms_template->render('ctypes_dataset', array(
            'do'      => 'add',
            'ctype'   => $ctype,
            'dataset' => isset($dataset) ? $dataset : array(),
            'form'    => $form,
            'errors'  => isset($errors) ? $errors : false
        ));

    }

}
