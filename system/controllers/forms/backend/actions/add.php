<?php

class actionFormsAdd extends cmsAction {

    public function run() {

        $do = 'add';

        $form_data = [];

        $form = $this->getForm('form', [$do, $form_data]);

        $is_submitted = $this->request->has('submit');

        if ($is_submitted) {

            $form_data = $form->parse($this->request, $is_submitted);

            $errors = $form->validate($this, $form_data);

            if (!$errors) {

                $id = $this->model->addForm($form_data);

                cmsUser::addSessionMessage(sprintf(LANG_FORMS_CP_FORMS_CREATED, $form_data['title']), 'success');

                return $this->redirectToAction('form_fields', [$id]);
            }

            if ($errors) {
                cmsUser::addSessionMessage(LANG_FORM_ERRORS, 'error');
            }
        }

        return $this->cms_template->render([
            'menu'      => $this->getFormMenu($do),
            'form_data' => $form_data,
            'do'        => $do,
            'form'      => $form,
            'errors'    => isset($errors) ? $errors : false
        ]);
    }

}
