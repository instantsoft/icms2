<?php
/**
 * @property \modelUsers $model_users
 * @property \modelBilling $model
 */
class actionBillingAddBalance extends cmsAction {

    public function run($user_id = false, $is_submitted = false) {

        if (!$this->cms_user->is_admin || !$this->request->isAjax()) {
            return cmsCore::error404();
        }

        if (!$user_id) {
            return cmsCore::error404();
        }

        $user = $this->model_users->getUser($user_id);

        if (!$user) {
            return cmsCore::error404();
        }

        $form = $this->getForm('balance');

        $options = [];

        if ($is_submitted) {

            $options = $form->parse($this->request, $is_submitted);
            $errors  = $form->validate($this, $options);

            if (!$errors) {

                $this->model->changeBalance('user', $user_id, $options['amount'], ($options['description'] ?: null));

                return $this->cms_template->renderJSON([
                    'errors'       => false,
                    'redirect_uri' => href_to_profile($user, ['balance'])
                ]);
            }

            if ($errors) {
                return $this->cms_template->renderJSON([
                    'errors' => $errors
                ]);
            }
        }

        return $this->cms_template->render([
            'options' => $options,
            'form'    => $form,
            'user_id' => $user_id,
            'errors'  => $errors ?? false
        ]);
    }

}
