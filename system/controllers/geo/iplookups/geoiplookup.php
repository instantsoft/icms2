<?php
class icmsGeoiplookup {

    public static $title = 'geoiplookup.net';

    public static function detect ($ip) {

        $xml = file_get_contents_from_url('http://api.geoiplookup.net/?query='.$ip);
        if(!$xml){ return false; }

        $out = simplexml_load_string($xml);
        if(!$out){ return false; }

        $data = array();

        foreach ((array)$out->results->result as $key => $value) {
            $key = ($key == 'countrycode' ? 'country' : $key);
            $key = ($key == 'latitude' ? 'lat' : $key);
            $key = ($key == 'longitude' ? 'lng' : $key);
            $data[$key] = (string)$value;
        }

        return $data;

    }

}
