<?php
/**
 * @property \modelUsers $model_users
 */
class actionAuthResubmit extends cmsAction {

    public function run() {

        if (!$this->options['is_reg_enabled']) {
            return cmsCore::error404();
        }

        if (empty($this->options['verify_email'])) {
            return cmsCore::error404();
        }

        if ($this->cms_user->is_logged && !$this->cms_user->is_admin) {
            return $this->redirectToHome();
        }

        $reg_email = cmsUser::getCookie('reg_email');

        if ($reg_email && $this->validate_email($reg_email) === true) {

            $reg_user = $this->model_users->filterNotNull('pass_token')->
                    filterEqual('is_locked', 1)->
                    getUserByEmail($reg_email);

            if (!$reg_user) {
                return cmsCore::error404();
            }

            $reg_user['resubmit_extime'] = modelAuth::RESUBMIT_TIME - (time() - strtotime($reg_user['date_token']));

        } else {

            return cmsCore::error404();
        }

        if ($reg_user['resubmit_extime'] > 0) {
            return cmsCore::errorForbidden();
        }

        $this->model_users->updateUser($reg_user['id'], [
            'date_token' => null
        ]);

        $verify_exp = $this->options['verify_exp'] ?? 48;

        $to     = ['email' => $reg_user['email'], 'name' => $reg_user['nickname']];
        $letter = ['name' => 'reg_verify'];

        cmsCore::getController('messages')->sendEmail($to, $letter, [
            'nickname'    => $reg_user['nickname'],
            'page_url'    => href_to_abs('auth', 'verify', $reg_user['pass_token']),
            'pass_token'  => $reg_user['pass_token'],
            'valid_until' => html_date(date('d.m.Y H:i', time() + ($verify_exp * 3600)), true)
        ]);

        cmsUser::addSessionMessage(sprintf(LANG_REG_SUCCESS_NEED_VERIFY, $reg_user['email']), 'info');

        cmsUser::setCookie('reg_email', $reg_user['email'], $verify_exp * 3600);

        return $this->redirectToAction('verify');
    }

}
