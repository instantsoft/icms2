<?php

class actionAdminWidgetsPageEdit extends cmsAction {

    public function run($id = false){

        if (!is_numeric($id)) { cmsCore::error404(); }

        $widgets_model = cmsCore::getModel('widgets');

        cmsCore::loadAllControllersLanguages();

        $page = $widgets_model->getPage($id);
        if (!$page) { cmsCore::error404(); }

        $form = $this->getForm('widgets_page');

        if (!$page['is_custom']){
            $form->removeField('title', 'title');
        }

        if (!$id){
            $form->removeField('urls', 'url_mask');
        }

        if ($this->request->has('submit')){

            $page = $form->parse($this->request, true);
            $errors = $form->validate($this,  $page);

            if (!$errors){

                $widgets_model->updatePage($id, $page);

                cmsUser::addSessionMessage(LANG_CP_SAVE_SUCCESS, 'success');

                $this->redirectToAction('widgets');

            }

            if ($errors){
                cmsUser::addSessionMessage(LANG_FORM_ERRORS, 'error');
            }


        }

        return $this->cms_template->render('widgets_page', array(
            'do'     => 'edit',
            'page'   => $page,
            'form'   => $form,
            'errors' => isset($errors) ? $errors : false
        ));

    }

}
