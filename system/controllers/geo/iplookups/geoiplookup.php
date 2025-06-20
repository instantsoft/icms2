<?php
/**
 * Провайдер сервиса ip-api.com
 * Имя класса оставлено для совместимости
 */
class icmsGeoiplookup {

    /**
     * Название провайдера для селекта формы
     * Должен возвращать массив с ячейками:
     * [
     *      'city'         => Название города,
     *      'country'      => Название страны,
     *      'country_code' => ISO код страны,
     *      'latitude'     => Широта,
     *      'longitude'    => Долгота
     *  ]
     * Или пустой массив
     *
     * @var string
     */
    public static $title = 'ip-api.com';

    /**
     * Основной метод, возвращающий массив с данными
     *
     * @param string $ip ip адрес
     * @return array
     */
    public static function detect($ip) {

        $result = file_get_contents_from_url('http://ip-api.com/json/' . $ip . '?lang=' . cmsCore::getLanguageName(), 3, true);

        if (!$result || empty($result['countryCode'])) {
            return [];
        }

        return [
            'city'         => $result['city'],
            'country'      => $result['country'],
            'country_code' => $result['countryCode'],
            'latitude'     => $result['lat'],
            'longitude'    => $result['lon']
        ];
    }

}
