<?php

class actionAdminCtypesDatasetsEdit extends cmsAction {

    public function run($dataset_id){

        if (!$dataset_id) { cmsCore::error404(); }

        $content_model = cmsCore::getModel('content');

        $dataset = $old_dataset = $content_model->getContentDataset($dataset_id);
        if (!$dataset) { cmsCore::error404(); }

        if($dataset['ctype_id']){

            $ctype = $content_model->getContentType($dataset['ctype_id']);
            if (!$ctype) { cmsCore::error404(); }

            $controller_name = 'content';

        } else {

            cmsCore::loadControllerLanguage($dataset['target_controller']);

            $ctype = array(
                'title' => string_lang($dataset['target_controller'].'_controller'),
                'name'  => $dataset['target_controller'],
                'id'    => null
            );

            $content_model->setTablePrefix('');

            $controller_name = $dataset['target_controller'];

        }

        $form = $this->getForm('ctypes_dataset', array('edit', ($ctype['id'] ? $ctype['id'] : $ctype['name'])));

        $cats_list = array();

        if($ctype['id']){

            $cats = $content_model->getCategoriesTree($ctype['name'], false);

            if ($cats){
                foreach($cats as $c){
                    $cats_list[$c['id']] = str_repeat('-- ', $c['ns_level']-1).' '.$c['title'];
                }
            }

        }

        $fields  = $content_model->getContentFields($ctype['name']);

        $fields = cmsEventsManager::hook('ctype_content_fields', $fields);

        if ($this->request->has('submit')){

            $dataset = $form->parse($this->request, true);

            $dataset['filters'] = $this->request->get('filters', array());
            $dataset['sorting'] = $this->request->get('sorting', array());

            $errors = $form->validate($this,  $dataset);

            if (!$errors){

                $content_model->updateContentDataset($dataset_id, $dataset, $ctype, $old_dataset);

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
            'do'          => 'edit',
            'ctype'       => $ctype,
            'dataset'     => $dataset,
            'fields_list' => $this->buildDatasetFieldsList($controller_name, $fields),
            'cats'        => $cats_list,
            'form'        => $form,
            'errors'      => isset($errors) ? $errors : false
        ));

    }

}
