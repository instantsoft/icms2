<?php

class onRecaptchaCaptchaValidate extends cmsAction {

    private $api_url = 'https://www.google.com/recaptcha/api/siteverify';

    public function run($request){

        $response = $request->get('g-recaptcha-response', false);
        if(!$response){ return false; }

        return $this->callApi(array(
            'secret'   => $this->options['private_key'],
            'response' => $response,
            'remoteip' => cmsUser::getIp()
        ));

    }

    private function callApi($params) {

        if (!function_exists('curl_init')){

            $data = @file_get_contents($this->api_url.'?'.http_build_query($params));

        } else {

            $curl = curl_init();

            if(strpos($this->api_url, 'https') !== false){
                curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
                curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            }
            curl_setopt($curl, CURLOPT_URL, $this->api_url);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_HEADER, false);
            curl_setopt($curl, CURLOPT_TIMEOUT, 5);
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $params);

            $data = curl_exec($curl);

            curl_close($curl);

        }

        if(!$data){ return false; }

        $data = json_decode($data, true);

        return !empty($data['success']);

    }

}
