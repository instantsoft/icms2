<?php

class actionAdminWidgetsPageEdit extends cmsAction {

    public function run($id=false){

        if (!$id) { cmsCore::error404(); }

        $widgets_model = cmsCore::getModel('widgets');
        
        cmsCore::loadAllControllersLanguages();
        
        $page = $widgets_model->getPage($id);
        
        if (!$page) { cmsCore::error404(); }

        $form = $this->getForm('widgets_page');
        
        if (!$page['is_custom']){
            $form->removeField('title', 'title');
        }        

        $is_submitted = $this->request->has('submit');

        if ($is_submitted){

            $page = $form->parse($this->request, $is_submitted);
            $errors = $form->validate($this,  $page);

            if (!$errors){

                $widgets_model->updatePage($id, $page);

                $this->redirectToAction('widgets');

            }

            if ($errors){

                cmsUser::addSessionMessage(LANG_FORM_ERRORS, 'error');

            }


        }

        return cmsTemplate::getInstance()->render('widgets_page', array(
            'do' => 'edit',
            'page' => $page,
            'form' => $form,
            'errors' => isset($errors) ? $errors : false
        ));
    }

}
