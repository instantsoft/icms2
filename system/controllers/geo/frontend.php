<?php

class geo extends cmsFrontend {

    protected $useOptions = true;

    /**
     * Возвращает массив гео данных
     * И в случае, если страна не определена,
     * Подставляется id страны по умолчанию
     *
     * @return array
     */
    public function getGeoByIp() {

        if (empty($this->options['auto_detect'])) {
            return false;
        }

        $geo = $this->getAutoDetectGeoByIp();

        if (!empty($this->options['default_country_id']) && empty($geo['country']['id']) && empty($geo['city']['country_id'])) {
            $geo['country']['id'] = $this->options['default_country_id'];
        }

        return $geo;
    }

    /**
     * Возвращает массив гео данных, определённых по ip адресу
     * Возвращаются данные из БД CMS, если определённые по ip
     * Совпадают с данными в таблицах cms_geo_countries и cms_geo_cities
     *
     * @param string $ip ip адрес
     * @return array
     */
    public function getAutoDetectGeoByIp($ip = '') {

        $geo = [
            'city' => [
                'id'   => null,
                'name' => null
            ],
            'region' => [
                'id'   => null,
                'name' => null
            ],
            'country' => [
                'id'   => null,
                'name' => null
            ]
        ];

        if (empty($this->options['auto_detect_provider'])) {
            return $geo;
        }

        if (!$ip) {
            $ip = cmsUser::getIp();
        }

        $cache_key = 'geo_data:' . md5($ip);

        $cached_geo = cmsUser::sessionGet($cache_key);
        if ($cached_geo) {
            return $cached_geo;
        }

        $data = $this->callProviderGeoData($this->options['auto_detect_provider'], $ip);

        if (!$data) {
            return $geo;
        }

        if (!empty($data['country_code'])) {

            $country = $this->model->getItemByField('geo_countries', 'alpha2', mb_strtoupper($data['country_code']));

            if ($country) {
                $geo['country'] = $country;
            }
        }

        if (!empty($data['city'])) {

            if (!empty($geo['country']['id'])) {
                $this->model->filterEqual('country_id', $geo['country']['id']);
            }

            $this->model->select('r.name', 'region_name')->
                    join('geo_regions', 'r', 'r.id = i.region_id')->
                    filterLike('name', $data['city'].'%');

            $city = $this->model->getItem('geo_cities');

            if ($city) {

                $geo['city']['id'] = $city['id'];
                $geo['city']['name'] = $city['id'];

                $geo['region']['id'] = $city['region_id'];
                $geo['region']['name'] = $city['region_name'];
            }
        }

        cmsUser::sessionSet($cache_key, $geo);

        return $geo;
    }

    /**
     * Возвращает гео данные от переданного провайдера
     *
     * @param string $provider Имя провайдера гео данных
     * @param string $ip ip адрес
     * @return array
     */
    public function callProviderGeoData($provider, $ip) {

        $geo_class_name = 'icms' . string_to_camel('_', $provider);

        if (!cmsCore::includeFile('system/controllers/geo/iplookups/' . $provider . '.php')) {
            return [];
        }

        return call_user_func([$geo_class_name, 'detect'], $ip);
    }

}
