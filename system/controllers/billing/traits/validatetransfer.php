<?php

namespace icms\controllers\billing\traits;

trait validatetransfer {

    public function before() {

        if (!$this->options['is_transfers']) {
            return \cmsCore::error404();
        }

        if (!$this->cms_user->is_logged) {
            return $this->redirectToLogin();
        }

        return parent::before();
    }

}
