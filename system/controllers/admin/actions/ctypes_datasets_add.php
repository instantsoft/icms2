<?php

class actionAdminCtypesDatasetsAdd extends cmsAction {

    public function run($ctype_id){

        if (!$ctype_id) { cmsCore::error404(); }

        $content_model = cmsCore::getModel('content');

        if(is_numeric($ctype_id)){

            $ctype = $content_model->getContentType($ctype_id);
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

            $content_model->setTablePrefix('');

            $controller_name = $ctype_id;

        }


        $form = $this->getForm('ctypes_dataset', array('add', ($ctype['id'] ? $ctype['id'] : $ctype['name'])));

        $fields  = $content_model->getContentFields($ctype['name']);

        $fields = cmsEventsManager::hook('ctype_content_fields', $fields);

        $cats_list = array();

        if($ctype['id']){

            $cats = $content_model->getCategoriesTree($ctype['name'], false);

            if ($cats){
                foreach($cats as $c){
                    $cats_list[$c['id']] = str_repeat('-- ', $c['ns_level']-1).' '.$c['title'];
                }
            }

        }

        $dataset = array('is_visible' => 1);

        if ($this->request->has('submit')){

			$dataset = $form->parse($this->request, true);

            $dataset['filters'] = $this->request->get('filters', array());
            $dataset['sorting'] = $this->request->get('sorting', array());

            $errors = $form->validate($this,  $dataset);

            if (!$errors){

                if(!$ctype['id']){
                    $dataset['target_controller'] = $controller_name;
                }

                $dataset_id = $content_model->addContentDataset($dataset, $ctype);

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
            'do'          => 'add',
            'ctype'       => $ctype,
            'dataset'     => $dataset,
            'fields_list' => $this->buildDatasetFieldsList($controller_name, $fields),
            'cats'        => $cats_list,
            'form'        => $form,
            'errors'      => isset($errors) ? $errors : false
        ));

    }

}
