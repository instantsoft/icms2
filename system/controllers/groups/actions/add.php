<?php

class actionGroupsAdd extends cmsAction {

    public function run(){

        if (!cmsUser::isAllowed('groups', 'add')) { cmsCore::error404(); }

        $form = $this->getForm('group');

        $is_submitted = $this->request->has('submit');

        $group = $form->parse($this->request, $is_submitted);

        if ($is_submitted){

            $errors = $form->validate($this, $group);

            if (!$errors){

                $id = $this->model->addGroup($group);

                $this->redirectToAction($id);

            }

            if ($errors){

                cmsUser::addSessionMessage(LANG_FORM_ERRORS, 'error');

            }

        }

        return cmsTemplate::getInstance()->render('group_edit', array(
            'do' => 'add',
            'group' => $group,
            'form' => $form,
            'errors' => isset($errors) ? $errors : false
        ));

    }

}
