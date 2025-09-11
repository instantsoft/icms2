<?php
/**
 * @property \modelBackendBilling $model
 */
class actionBillingAddBalance extends cmsAction {

    public function run() {

        $form = $this->getForm('balance');

        $is_submitted = $this->request->has('submit');

        $options = [
            'mode'       => $this->request->get('mode', 'user'),
            'user_email' => $this->request->get('user_email', '')
        ];

        if ($is_submitted) {

            $options = $form->parse($this->request, $is_submitted);

            $errors = $form->validate($this, $options);

            if ($options['mode'] === 'user') {

                if (!$options['user_email']) {

                    $errors['user_email'] = ERR_VALIDATE_REQUIRED;

                } else {

                    $user = $this->model_users->getUserByEmail($options['user_email']);
                    if (!$user && !$errors) {
                        $errors['user_email'] = ERR_USER_NOT_FOUND;
                    }
                }
            }

            if (!$errors) {

                if (!empty($user['id'])) {
                    $subject_id = $user['id'];
                } else {
                    $subject_id = $options['group_id'];
                }

                $this->model->changeBalance($options['mode'], $subject_id, $options['amount'], ($options['description'] ?: null));

                cmsUser::addSessionMessage(LANG_BILLING_CP_BAL_SUCCESS, 'success');

                return $this->redirectToAction('log');
            }

            if ($errors) {
                cmsUser::addSessionMessage(LANG_FORM_ERRORS, 'error');
            }
        }

        return $this->cms_template->render('backend/add_balance', [
            'options' => $options,
            'form'    => $form,
            'errors'  => $errors ?? false
        ]);

    }
}
