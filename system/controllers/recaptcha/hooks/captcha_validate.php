<?php

class onRecaptchaCaptchaValidate extends cmsAction {

    public function run($request){

        $this->includeRecaptchaLib();
        
        $key = $this->options['private_key'];
        $client_ip = $_SERVER["REMOTE_ADDR"];
        $challenge = $request->get('recaptcha_challenge_field');
        $response = $request->get('recaptcha_response_field');
        
        $result = recaptcha_check_answer ($key, $client_ip, $challenge, $response);
        
        return $result->is_valid;

    }

}
