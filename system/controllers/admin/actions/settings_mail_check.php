<?php

class actionAdminSettingsMailCheck extends cmsAction {

    public function run(){

        $form = $this->getForm('mail_check');

       if ($this->request->has('email')){

            $values = $form->parse($this->request, true);
            $errors = $form->validate($this,  $values);

            if (!$errors){

                $mailer = new cmsMailer();

                $mailer->addTo($values['email']);
                $mailer->setSubject($values['subject']);
                $mailer->setBodyHTML(nl2br($values['body']));

                $result = $mailer->send();

                $mailer->clearTo()->clearAttachments();

                return $this->cms_template->renderJSON(array(
                    'errors'   => false,
                    'type'     => ($result ? '' : 'ui_error'),
                    'text'     => ($result ? LANG_MAILCHECK_SUCCESS : sprintf(LANG_MAILCHECK_ERROR, $mailer->getErrorInfo())),
                    'callback' => 'checkSuccess'
                ));

            } else {

                return $this->cms_template->renderJSON(array(
                    'errors' => $errors,
                ));

            }

        }

        return $this->cms_template->render('mail_check', array(
            'values' => isset($values) ? $values : array(),
            'form'   => $form,
            'errors' => isset($errors) ? $errors : false
        ));

    }

}
