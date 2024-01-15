<?php
/**
 * Провайдер сервиса ip.nf
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
    public static $title = 'ip.nf';

    /**
     * Основной метод, возвращающий массив с данными
     *
     * @param string $ip ip адрес
     * @return array
     */
    public static function detect($ip) {

        $result = file_get_contents_from_url('https://ip.nf/' . $ip . '.json', 3, true);

        if (!$result || empty($result['ip'])) {
            return [];
        }

        return [
            'city'         => $result['ip']['city'],
            'country'      => $result['ip']['country'],
            'country_code' => $result['ip']['country_code'],
            'latitude'     => $result['ip']['latitude'],
            'longitude'    => $result['ip']['longitude']
        ];
    }

}
