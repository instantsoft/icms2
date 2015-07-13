<?php

class actionAdminCtypesDatasetsEdit extends cmsAction {

    public function run($ctype_id, $dataset_id){

        if (!$ctype_id || !$dataset_id) { cmsCore::error404(); }

        $content_model = cmsCore::getModel('content');

        $ctype = $content_model->getContentType($ctype_id);
        if (!$ctype) { cmsCore::error404(); }

        $form = $this->getForm('ctypes_dataset', array('edit', $ctype['id']));

        $is_submitted = $this->request->has('submit');

        $dataset = $content_model->getContentDataset($dataset_id);

        $fields  = $content_model->getContentFields($ctype['name']);

        if ($is_submitted){

            $dataset = $form->parse($this->request, $is_submitted);

            $dataset['ctype_id']    = $ctype['id'];
            $dataset['filters']     = $this->request->get('filters');
            $dataset['sorting']     = $this->request->get('sorting');

            $errors = $form->validate($this,  $dataset);

            if (!$errors){

                $content_model->updateContentDataset($dataset_id, $dataset);

                $this->redirectToAction('ctypes', array('datasets', $ctype['id']));

            }

            if ($errors){

                cmsUser::addSessionMessage(LANG_FORM_ERRORS, 'error');

            }

        }

        return cmsTemplate::getInstance()->render('ctypes_dataset', array(
            'do' => 'edit',
            'ctype' => $ctype,
            'dataset' => $dataset,
            'fields' => $fields,
            'form' => $form,
            'errors' => isset($errors) ? $errors : false
        ));

    }

}
