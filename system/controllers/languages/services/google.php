<?php

namespace icms\controllers\languages\services;

use icms\controllers\languages\translatable;

class google implements translatable {

    public function translate($source_lang, $target_lang, $text) {

        $url = 'https://translate.google.com/translate_a/single?client=at&dt=t&dt=ld&dt=qca&dt=rm&dt=bd&dj=1&hl=ru-RU&ie=UTF-8&oe=UTF-8&inputm=2&otf=2&iid=1dd3b944-fa62-4b55-b330-74909a99969e&';

        $result = $this->request($url, [
            'sl' => $source_lang,
            'tl' => $target_lang,
            'q'  => $text
        ]);

        $sentences = '';

        if (!empty($result['sentences'])) {
            foreach ($result['sentences'] as $s) {
                $sentences .= isset($s['trans']) ? ' ' . $s['trans'] : '';
            }
        }

        return $sentences;
    }

    private function request($url, $fields) {

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, count($fields));
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($fields));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_ENCODING, 'UTF-8');
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($ch, CURLOPT_USERAGENT, 'AndroidTranslate/5.3.0.RC02.130475354-53000263 5.1 phone TRANSLATE_OPM5_TEST_1');

        $result   = curl_exec($ch);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        curl_close($ch);

        if (false === $result || 200 !== $httpcode) {

            return [];
        }

        return json_decode($result, true);
    }

}
