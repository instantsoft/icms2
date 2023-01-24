<?php

class actionAdminConfirmLogin extends cmsAction {

    public function run($form, $errors, $pagetitle, $title, $hint) {

        return $this->cms_template->render('confirm_login', [
            'user'      => $this->cms_user,
            'form'      => $form,
            'pagetitle' => $pagetitle,
            'title'     => $title,
            'hint'      => $hint,
            'errors'    => isset($errors) ? $errors : false
        ]);
    }

}
