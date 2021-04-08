<?php

class actionAdminWidgetsPageAdd extends cmsAction {

    public function run() {

        $form = $this->getForm('widgets_page');

        $is_submitted = $this->request->has('submit');

        $page = $form->parse($this->request, $is_submitted);

        if ($is_submitted) {

            $errors = $form->validate($this, $page);

            if (!$errors) {

                $page_id = $this->model_backend_widgets->addPage($page);

                if ($page_id) {

                    cmsUser::addSessionMessage(sprintf(LANG_CP_WIDGET_PAGE_CREATED, $page['title']), 'success');

                    cmsUser::setCookiePublic('widgets_tree_path', "/custom/custom.{$page_id}");
                }

                $this->redirectToAction('widgets');
            }

            if ($errors) {
                cmsUser::addSessionMessage(LANG_FORM_ERRORS, 'error');
            }
        }

        return $this->cms_template->render('widgets_page', [
            'do'     => 'add',
            'page'   => $page,
            'form'   => $form,
            'errors' => isset($errors) ? $errors : false
        ]);
    }

}
