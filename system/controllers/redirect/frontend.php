<?php

class redirect extends cmsFrontend {

    protected $useOptions = true;

    public function actionIndex() {

        $this->cms_core->response->setHeader('X-Frame-Options', 'DENY');

        $url = trim(urldecode($this->request->get('url', '')));
        if (!$url) {
            return cmsCore::error404();
        }

        // $original_url для кириллических доменов
        $original_url = $url;

        if ($this->request->isAjax()) {
            return cmsCore::error404();
        }

        $url_host = parse_url($url, PHP_URL_HOST);
        if (!$url_host) {
            return cmsCore::error404();
        }

        if (!empty($this->options['is_check_refer'])) {

            $referer = $this->request->getHeader('REFERER');

            if (!$referer) {
                return cmsCore::error404();
            }

            if (strpos($referer, $this->cms_config->host) !== 0) {
                return cmsCore::error404();
            }
        }

        // кириллические домены
        if (preg_match('/^[а-яё]+/iu', $url_host)) {

            $IDN = new idna_convert();

            $host = $IDN->encode($url_host);

            $url = str_ireplace($url_host, $host, $url);
        }

        // ссылки, по которым редиректить сразу
        if (!empty($this->options['no_redirect_list_array'])) {
            if (in_array($url_host, $this->options['no_redirect_list_array'], true)) {
                return $this->redirect($url);
            }
        }

        // флаг бана домена
        $is_domain_banned = false;
        // флаг того, что домен в нашем черном списке
        $is_domain_in_black_list = false;

        // сначала проверяем черный список
        if (!empty($this->options['black_list_array'])) {
            if (in_array($url_host, $this->options['black_list_array'])) {
                $is_domain_banned        = true;
                $is_domain_in_black_list = true;
            }
        }

        // если пользователь поставил "печеньку"
        if (!$is_domain_in_black_list && $this->cms_user->is_logged && cmsUser::getCookie('allow_redirect')) {
            return $this->redirect($url);
        }

        // теперь проверяем ссылку
        if (!$is_domain_banned && function_exists('curl_init') && !empty($this->options['is_check_link']) && !empty($this->options['vk_access_token'])) {

            if (empty($this->options['white_list_array']) ||
                    ($this->options['white_list_array'] && !in_array($url_host, $this->options['white_list_array']))) {

                $params = [
                    'v'=> '5.199',
                    'access_token'=> $this->options['vk_access_token'],
                    'url'=> $url
                ];

                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, 'https://api.vk.ru/method/utils.checkLink?' . http_build_query($params));
                curl_setopt($ch, CURLOPT_HEADER, false);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_TIMEOUT, 2);
                curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 2);
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch, CURLOPT_USERAGENT, 'InstantCMS/2.0 +' . cmsConfig::get('host'));

                $data = json_decode(curl_exec($ch), true);

                if (!isset($data['error']) && isset($data['response'])) {
                    $is_domain_banned = ($data['response']['status'] === 'banned');
                    $url = $data['response']['link'];
                }
            }
        }

        return $this->cms_template->render('index', [
            'url'                     => html($url, false),
            'user'                    => $this->cms_user,
            'original_url'            => html($original_url, false),
            'is_domain_banned'        => $is_domain_banned,
            'is_domain_in_black_list' => $is_domain_in_black_list,
            'sitename'                => cmsConfig::get('sitename'),
            'host'                    => cmsConfig::get('host'),
            'redirect_time'           => $this->options['redirect_time']
        ]);
    }

    public function getOptions() {

        $options = parent::getOptions();

        if (!empty($options['no_redirect_list'])) {

            $no_redirect_list = explode("\n", $options['no_redirect_list']);

            $options['no_redirect_list_array'] = array_map(function ($val) {
                return trim($val);
            }, $no_redirect_list);
        }

        if (!empty($options['black_list'])) {

            $black_list = explode("\n", $options['black_list']);

            $options['black_list_array'] = array_map(function ($val) {
                return trim($val);
            }, $black_list);
        }

        if (!empty($options['white_list'])) {

            $white_list = explode("\n", $options['white_list']);

            $options['white_list_array'] = array_map(function ($val) {
                return trim($val);
            }, $white_list);
        }

        return $options;
    }

}
