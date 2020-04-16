<?php
class icmsIpgeobase {

    public static $title = 'ipgeobase.ru';

    public static function detect ($ip) {

        $xml = file_get_contents_from_url('http://ipgeobase.ru:7020/geo?ip='.$ip);
        if(!$xml){ return false; }

        $out = simplexml_load_string($xml);
        if(!$out){ return false; }

        $data = array();

        if($out && is_object($out) && !empty($out->ip[0])){
            foreach ($out->ip[0] as $key=>$value) {
                $data[$key] = (string)$value;
            }
        }

        return $data;

    }

}
