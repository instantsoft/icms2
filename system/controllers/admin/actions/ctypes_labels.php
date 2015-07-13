<?php

class actionAdminCtypesLabels extends cmsAction {

    public function run($id){

        if (!$id) { cmsCore::error404(); }

        $wizard_mode = $this->request->get('wizard_mode');

        $form = $this->getForm('ctypes_labels');

        $is_submitted = $this->request->has('submit');

        $content_model = cmsCore::getModel('content');

        $ctype = $content_model->getContentType($id);
        if (!$ctype) { cmsCore::error404(); }

        cmsCore::loadControllerLanguage('content');

        if ($is_submitted){

            $ctype = array_merge($ctype, $form->parse($this->request, $is_submitted));

            $errors = $form->validate($this,  $ctype);

            if (!$errors){

                $content_model->updateContentType($id, $ctype);

                $activity_controller = cmsCore::getController('activity');

                if ($activity_controller->isTypeExists('content', "add.{$ctype['name']}")){

                    $activity_controller->updateType('content', "add.{$ctype['name']}", array(
                        'title' => sprintf(LANG_CONTENT_ACTIVITY_ADD, $ctype['labels']['many']),
                        'description' => sprintf(LANG_CONTENT_ACTIVITY_ADD_DESC, $ctype['labels']['create'], '%s')
                    ));

                } else {

                    $activity_controller->addType(array(
                        'controller' => 'content',
                        'name' => "add.{$ctype['name']}",
                        'title' => sprintf(LANG_CONTENT_ACTIVITY_ADD, $ctype['labels']['many']),
                        'description' => sprintf(LANG_CONTENT_ACTIVITY_ADD_DESC, $ctype['labels']['create'], '%s')
                    ));

                }

                if ($wizard_mode){
                    $this->redirectToAction('ctypes', array('fields', $id), array('wizard_mode'=>true));
                } else {
                    $this->redirectToAction('ctypes');
                }

            }

            if ($errors){

            cmsUser::addSessionMessage(LANG_FORM_ERRORS, 'error');

            }

        }

        return cmsTemplate::getInstance()->render('ctypes_labels', array(
            'id' => $id,
            'ctype' => $ctype,
            'form' => $form,
            'errors' => isset($errors) ? $errors : false
        ));

    }

}
