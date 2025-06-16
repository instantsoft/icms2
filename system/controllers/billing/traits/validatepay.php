<?php

namespace icms\controllers\billing\traits;

trait validatepay {

    public function before() {

        if ($this->options['in_mode'] === 'disabled') {
            return \cmsCore::error404();
        }

        if (!$this->cms_user->is_logged) {
            return $this->redirectToLogin();
        }

        if (!empty($this->use_csrf_token)) {

            if (!\cmsForm::validateCSRFToken($this->request->get('csrf_token', ''))) {

                \cmsUser::addSessionMessage(\LANG_FORM_ERRORS, 'error');

                if (!$this->request->isAjax()) {
                    return $this->redirectToAction('deposit');
                }

                return $this->cms_template->renderJSON([
                    'error' => true
                ]);
            }
        }

        return parent::before();
    }

}
