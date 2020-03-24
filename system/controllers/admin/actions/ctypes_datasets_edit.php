<?php

class actionAdminCtypesDatasetsEdit extends cmsAction {

    public function run($dataset_id){

        if (!$dataset_id) { cmsCore::error404(); }

        $dataset = $old_dataset = $this->model_content->getContentDataset($dataset_id);
        if (!$dataset) { cmsCore::error404(); }

        if($dataset['ctype_id']){

            $ctype = $this->model_content->getContentType($dataset['ctype_id']);
            if (!$ctype) { cmsCore::error404(); }

            $controller_name = 'content';

        } else {

            cmsCore::loadControllerLanguage($dataset['target_controller']);

            $ctype = array(
                'title' => string_lang($dataset['target_controller'].'_controller'),
                'name'  => $dataset['target_controller'],
                'id'    => null
            );

            $this->model_content->setTablePrefix('');

            $controller_name = $dataset['target_controller'];

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

        $form = $this->getForm('ctypes_dataset', array('edit', $ctype, $cats_list, $fields_list));

        if ($this->request->has('submit')){

            $dataset = $form->parse($this->request, true);

            $errors = $form->validate($this,  $dataset);

            if (!$errors){

                $this->model_content->updateContentDataset($dataset_id, $dataset, $ctype, $old_dataset);

                cmsUser::addSessionMessage(LANG_CP_SAVE_SUCCESS, 'success');

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
            'do'      => 'edit',
            'ctype'   => $ctype,
            'dataset' => $dataset,
            'form'    => $form,
            'errors'  => isset($errors) ? $errors : false
        ));

    }

}
