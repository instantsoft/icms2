<?php

class actionAdminSettingsMailCheck extends cmsAction {

    public function run() {

        $form = $this->getForm('mail_check');

        if ($this->request->has('email')) {

            $values = $form->parse($this->request, true);

            $errors = $form->validate($this, $values);

            if (!$errors) {

                $result = cmsCore::getController('messages')->sendEmailRaw(
                    [
                        'email' => $values['email'],
                        'name' => false
                    ],
                    [
                        'text' => '[subject:'.$values['subject'].']' . $values['body']
                    ],
                    true
                );

                return $this->cms_template->renderJSON([
                    'errors'   => false,
                    'type'     => ($result === true ? '' : 'ui_error'),
                    'text'     => ($result === true ? LANG_MAILCHECK_SUCCESS : sprintf(LANG_MAILCHECK_ERROR, $result)),
                    'callback' => 'checkSuccess'
                ]);

            } else {

                return $this->cms_template->renderJSON([
                    'errors' => $errors,
                ]);
            }
        }

        return $this->cms_template->render('mail_check', [
            'values' => isset($values) ? $values : [],
            'form'   => $form,
            'errors' => isset($errors) ? $errors : false
        ]);
    }

}
