<?php
/**
 * @property \modelBackendWidgets $model_backend_widgets
 */
class actionAdminWidgetsPageAdd extends cmsAction {

    public function run($copy_id = null) {

        $form = $this->getForm('widgets_page');

        $is_submitted = $this->request->has('submit');

        if ($copy_id) {

            cmsCore::loadAllControllersLanguages();

            $page = $this->model_backend_widgets->getPage($copy_id);
            if (!$page) {
                return cmsCore::error404();
            }

        } elseif(!$is_submitted) {
            $page = $form->parse($this->request, $is_submitted);
        }

        if ($is_submitted) {

            $page   = $form->parse($this->request, $is_submitted);
            $errors = $form->validate($this, $page);

            if (!$errors) {

                $page_id = $this->model_backend_widgets->addPage($page);

                if ($page_id) {

                    cmsUser::addSessionMessage(sprintf(LANG_CP_WIDGET_PAGE_CREATED, $page['title']), 'success');

                    cmsUser::setCookiePublic('widgets_tree_path', "/custom/custom.{$page_id}");
                }

                return $this->redirectToAction('widgets');
            }

            if ($errors) {
                cmsUser::addSessionMessage(LANG_FORM_ERRORS, 'error');
            }
        }

        return $this->cms_template->render('widgets_page', [
            'do'     => 'add',
            'page'   => $page,
            'form'   => $form,
            'errors' => $errors ?? false
        ]);
    }

}
