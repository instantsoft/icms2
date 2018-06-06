<?php
class actionAuthResubmit extends cmsAction {

    public function run(){

        if (empty($this->options['verify_email'])){
            cmsCore::error404();
        }

        if ($this->cms_user->is_logged && !$this->cms_user->is_admin) { $this->redirectToHome(); }

        $users_model = cmsCore::getModel('users');

        $reg_email = cmsUser::getCookie('reg_email');

        if($reg_email && $this->validate_email($reg_email) === true){

            $reg_user = $users_model->getUserByEmail($reg_email);

            $reg_user['resubmit_extime'] = modelAuth::RESUBMIT_TIME - (time() - strtotime($reg_user['date_token']));

        } else {
            return cmsCore::error404();
        }

        if($reg_user['resubmit_extime'] > 0){
            return cmsCore::errorForbidden();
        }

        $users_model->updateUser($reg_user['id'], array(
            'date_token' => null
        ));

        $verify_exp = empty($this->options['verify_exp']) ? 48 : $this->options['verify_exp'];

        $to = array('email' => $reg_user['email'], 'name' => $reg_user['nickname']);
        $letter = array('name' => 'reg_verify');

        cmsCore::getController('messages')->sendEmail($to, $letter, array(
            'nickname'    => $reg_user['nickname'],
            'page_url'    => href_to_abs('auth', 'verify', $reg_user['pass_token']),
            'pass_token'  => $reg_user['pass_token'],
            'valid_until' => html_date(date('d.m.Y H:i', time() + ($verify_exp * 3600)), true)
        ));

        cmsUser::addSessionMessage(sprintf(LANG_REG_SUCCESS_NEED_VERIFY, $reg_user['email']), 'info');

        cmsUser::setCookie('reg_email', $reg_user['email'], $verify_exp*3600);

        $this->redirectToAction('verify');

    }

}
