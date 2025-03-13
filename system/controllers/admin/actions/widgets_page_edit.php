<?php
/**
 * @property \modelBackendWidgets $model_backend_widgets
 */
class actionAdminWidgetsPageEdit extends cmsAction {

    public function run($id = false) {

        if (!is_numeric($id)) {
            return cmsCore::error404();
        }

        cmsCore::loadAllControllersLanguages();

        $page = $this->model_backend_widgets->getPage($id);

        if (!$page) {
            return cmsCore::error404();
        }

        $form = $this->getForm('widgets_page');

        if (!$page['is_custom']) {
            $form->removeField('title', 'title');
        }

        if (!$id) {
            $form->removeField('urls', 'url_mask');
            $form->removeField('title', 'layout');
        }

        if ($this->request->has('submit')) {

            $page   = $form->parse($this->request, true);
            $errors = $form->validate($this, $page);

            if (!$errors) {

                $this->model_backend_widgets->updatePage($id, $page);

                cmsUser::addSessionMessage(LANG_CP_SAVE_SUCCESS, 'success');

                return $this->redirectToAction('widgets');
            }

            if ($errors) {
                cmsUser::addSessionMessage(LANG_FORM_ERRORS, 'error');
            }
        }

        return $this->cms_template->render('widgets_page', [
            'do'     => 'edit',
            'page'   => $page,
            'form'   => $form,
            'errors' => $errors ?? false
        ]);
    }

}
