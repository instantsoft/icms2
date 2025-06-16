<?php

namespace icms\controllers\billing\traits;

trait validateout {

    public function before() {

        if (!$this->options['is_out']) {
            return \cmsCore::error404();
        }

        if (!$this->cms_user->is_logged) {
            return $this->redirectToLogin();
        }

        if (!$this->cms_user->isInGroups($this->options['out_groups'])) {
            return \cmsCore::error404();
        }

        return parent::before();
    }

}
