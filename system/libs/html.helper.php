<?php

/**
 * Экранирует значение для безопасного вывода в HTML
 *
 * @param mixed $string Значение для экранирования
 * @param bool $print Печатать результат (true) или вернуть как строку (false). По умолчанию true.
 * @return string|null Экранированная строка или null, если напечатано
 */
function html($string, $print = true) {

    $escaped = htmlentities((string)$string, ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML401, 'UTF-8');

    if ($print) {
        echo $escaped;
        return null;
    }

    return $escaped;
}

/**
 * Очищает строку от тегов и обрезает до нужной длины
 *
 * @param mixed  $string     Входной HTML-текст
 * @param ?int   $max_length Максимальное кол-во символов, по умолчанию — не обрезать
 * @return string
 */
function html_clean($string, $max_length = null) {

    // строка может быть без переносов
    // и после strip_tags не будет пробелов между словами
    $string = str_ireplace(
            ['<br>', '<br/>', '<br />'],
            ' ',
            (string) $string
    );

    $string = trim(preg_replace('/\s+/u', ' ', strip_tags($string)));

    if ($max_length) {
        $string = html_strip($string, $max_length);
    }

    return $string;
}

/**
 * Обрезает строку до заданного кол-ва символов
 * @param string $string Строка
 * @param integer $max_length Кол-во символов, которые нужно оставить от начала строки
 * @return string
 */
function html_strip($string, $max_length) {

    $length = mb_strlen($string);

    if ($length > $max_length) {
        $string = mb_substr($string, 0, (int)$max_length);
        $string .= '...';
    }

    return $string;
}

/**
 * Формирует ссылку по относительной (без добавления корня URL)
 * @param string $rel_link
 * @param boolean $is_abs
 * @return string
 */
function rel_to_href($rel_link, $is_abs = false) {

    $lang_href = cmsCore::getLanguageHrefPrefix();

    return ($is_abs ? cmsConfig::get('host') . '/' : cmsConfig::get('root')) . ($lang_href ? $lang_href . '/' : '') . $rel_link;
}

/**
 * Возвращает ссылку на профиль пользователя
 *
 * @param mixed $user Массив данных пользователя
 * @param mixed $params
 * @param boolean $is_abs
 * @return string
 */
function href_to_profile($user, $params = false, $is_abs = false){

    $href_func = 'href_to';
    if($is_abs){
        $href_func = 'href_to_abs';
    }

    if(is_array($user)){
        return $href_func('users', (empty($user['slug']) ? $user['id'] : $user['slug']), $params);
    } elseif(is_object($user)){
        return $href_func('users', (empty($user->slug) ? $user->id : $user->slug), $params);
    }

    return $href_func('users', $user, $params);
}

/**
 * Возвращает ссылку на указанное действие контроллера
 * с добавлением пути от корня сайта
 * @param string $controller Имя контроллера
 * @param string $action Имя экшена
 * @param array|string|integer $params Параметры экшена
 * @param array $query Параметры строки запроса
 * @return string
 */
function href_to($controller, $action = '', $params = false, $query = []){

    $lang_href = cmsCore::getLanguageHrefPrefix();

    return cmsConfig::get('root') .($lang_href ? $lang_href.'/' : ''). href_to_rel($controller, $action, $params, $query);
}

/**
 * Возвращает ссылку на указанное действие контроллера
 * с добавлением хоста сайта
 * @param string $controller
 * @param string $action
 * @param array|string|integer $params Параметры, массив
 * @return string
 */
function href_to_abs($controller, $action = '', $params = false, $query = []){

    $lang_href = cmsCore::getLanguageHrefPrefix();

    return cmsConfig::get('host') . '/' .($lang_href ? $lang_href.'/' : ''). href_to_rel($controller, $action, $params, $query);
}

/**
 * Возвращает ссылку на указанное действие контроллера без добавления корня URL
 *
 * @param string $controller Имя контроллера
 * @param string $action Имя действия
 * @param array|string|integer $params Параметры, массив
 * @param array $query Параметры строки запроса
 * @return string
 */
function href_to_rel($controller, $action = '', $params = false, $query = []) {

    $controller = trim($controller, '/ ');

    $ctype_default = cmsConfig::get('ctype_default');

    if ($ctype_default && $action && in_array($controller, $ctype_default)) {
        if (preg_match('/[a-z0-9\-\/{}]+(\.html|\/view\-[a-z0-9\-_]+)$/i', $action)) {
            $controller = '';
        }
    }

    $controller_alias = cmsCore::getControllerAliasByName($controller);
    if ($controller_alias) {
        $controller = $controller_alias;
    }

    $url_parts = [];

    if ($controller) {
        $url_parts[] = $controller;
    }

    if ($action) {
        $url_parts[] = $action;
    }

    if ($params) {
        $url_parts[] = is_array($params) ? implode('/', $params) : $params;
    }

    // trim, на случай если в $params пустые значения
    $href = trim(implode('/', $url_parts), '/');

    if ($query) {
        $href .= '?' . http_build_query($query, '', '&');
    }

    return $href;
}

/**
 * Возвращает ссылку на текущую страницу
 *
 * @param bool $add_host Добавлять http://ваш-сайт.ру
 * @return string
 */
function href_to_current($add_host = false) {

    static $rel_url = null;

    if ($rel_url === null) {

        $lang_href = cmsCore::getLanguageHrefPrefix();
        $lang_href = ($lang_href ? '/' . $lang_href : '');

        $rel_url = $lang_href . cmsCore::getInstance()->request->getServer('REQUEST_URI');
    }

    if ($add_host) {
        return cmsConfig::get('host') . $rel_url;
    } else {
        return $rel_url;
    }
}

/**
 * Возвращает ссылку на главную страницу сайта
 * @return string
 */
function href_to_home($add_host = false){
    return ($add_host ? cmsConfig::get('host').'/' : cmsConfig::get('root')).cmsCore::getLanguageHrefPrefix();
}

/**
 * Возвращает отформатированную строку аттрибутов тега
 *
 * @param array $attributes Атрибуты тега название=>значение
 * @param boolean $unset_class_key Не формировать CSS класс
 * @return string
 */
function html_attr_str($attributes, $unset_class_key = true) {

    if (!$attributes || !is_array($attributes)) {
        return '';
    }

    $attr_parts = [];

    if ($unset_class_key) {
        unset($attributes['class']);
    }

    foreach ($attributes as $key => $val) {
        if (is_bool($val)) {
            if ($val) {
                $attr_parts[] = $key;
            }
        } else {
            // Формируем строку атрибута с экранированием значения
            $attr_parts[] = $key . '="' . html((!is_array($val) ? $val : implode(' ', $val)), false) . '"';
        }
    }

    return implode(' ', $attr_parts);
}

/**
 * Печатает короткий HTML тег
 * @param string $tag_name Имя тега
 * @param array $attributes Атрибуты тега название=>значение
 * @param string $class CSS класс, если он отдельно от атрибутов
 * @return string
 */
function html_tag_short($tag_name, $attributes, $class = '') {

    if(!empty($attributes['class'])){
        $class .= ' ' . $attributes['class'];
    }
    $attributes['class'] = trim($class);

    return '<'.$tag_name.' ' . html_attr_str($attributes, false) . '>';
}

function default_images($type, $preset) {
    return [
        $preset => 'default/' . $type . '_' . $preset . '.png'
    ];
}

/**
 * Возвращает ссылку на аватар пользователя
 * @param array|yaml $avatars Все изображения аватара
 * @param string $size_preset Название пресета
 * @return string
 */
function html_avatar_image_src($avatars, $size_preset = 'small', $is_relative = true) {

    $config = cmsConfig::getInstance();

    if (!is_array($avatars)) {
        $avatars = cmsModel::yamlToArray($avatars);
    }

    if (!$avatars || empty($avatars[$size_preset])) {
        $avatars = [
            $size_preset => 'default/avatar.jpg',
            'normal' => 'default/avatar.jpg',
            'small'  => 'default/avatar_small.jpg',
            'micro'  => 'default/avatar_micro.png'
        ];

    }

    $src = $avatars[$size_preset];

    if (strpos($src, $config->upload_host) === false) {
        if ($is_relative) {
            $src = $config->upload_host . '/' . $src;
        } else {
            $src = $config->upload_host_abs . '/' . $src;
        }
    }

    return html($src, false);
}

/**
 * Возвращает путь к файлу изображения
 * @param array|yaml $image Все размеры заданного изображения
 * @param string $size_preset Название пресета
 * @param bool $is_add_host Возвращать путь отностительно директории хранения или полный путь
 * @param bool $is_relative Возвращать относительный путь или всегда с полным url
 * @return boolean|string
 */
function html_image_src($image, $size_preset = 'small', $is_add_host = false, $is_relative = true) {

    $config = cmsConfig::getInstance();

    if (!is_array($image)) {
        $image = cmsModel::yamlToArray($image);
    }

    if (!$image) {
        return false;
    }

    $keys = array_keys($image);
    if ($keys[0] === 0) {
        $image = $image[0];
    }

    if (isset($image[$size_preset])) {
        $src = $image[$size_preset];
    } else {
        return false;
    }

    if ($is_add_host && strpos($src, $config->upload_host) === false) {
        if ($is_relative) {
            $src = $config->upload_host . '/' . $src;
        } else {
            $src = $config->upload_host_abs . '/' . $src;
        }
    }

    return html($src, false);
}

/**
 * Возвращает код wysiwyg редатора
 *
 * @param string $field_name Имя элемента
 * @param string $content Текст редактора
 * @param string $wysiwyg Имя редактора
 * @param array $config Параметры редактора
 * @return string HTML код
 */
function html_wysiwyg($field_name, $content = '', $wysiwyg = false, $config = []) {

    $dom_id = !empty($config['id']) ? $config['id'] : str_replace(['[',']'], ['_', ''], $field_name);

    if (!$wysiwyg) {

        if ($wysiwyg === null) {
            return '<textarea class="textarea form-control" rows="5" id="' . $dom_id . '" name="' . $field_name . '">' . html($content, false) . '</textarea>';
        }

        $wysiwyg = cmsConfig::get('default_editor');
    }

    $connector = 'wysiwyg/' . $wysiwyg . '/wysiwyg.class.php';

    if (!cmsCore::includeFile($connector)) {
        return '<textarea class="error_wysiwyg" id="' . $dom_id . '" name="' . $field_name . '">' . html($content, false) . '</textarea>';
    }

    cmsCore::loadControllerLanguage($wysiwyg);

    list($field_name, $content, $wysiwyg, $config) = cmsEventsManager::hook(['display_wysiwyg_editor', 'display_' . $wysiwyg . '_wysiwyg_editor'], [$field_name, $content, $wysiwyg, $config]);

    $class_name = 'cmsWysiwyg' . ucfirst($wysiwyg);

    $editor = new $class_name($config);

    // $config передаём для совместимости
    ob_start();
    $editor->displayEditor($field_name, $content, $config);

    return ob_get_clean();
}

/**
 * Редактор markitup
 * функция совместимости
 *
 * @param string $field_name
 * @param string $content
 * @param array $options
 * @return string
 */
function html_editor($field_name, $content = '', $options = []) {
    return html_wysiwyg($field_name, $content, 'markitup', $options);
}

function html_select_range($name, $start, $end, $step, $add_lead_zero = false, $selected = '', $attributes = []) {

    $items = [];

    for ($i = $start; $i <= $end; $i += $step) {
        if ($add_lead_zero) {
            $i = $i > 9 ? $i : "0{$i}";
        }
        $items[$i] = $i;
    }

    return html_select($name, $items, $selected, $attributes);
}

/**
 * Возвращает строку содержащую число со знаком плюс или минус
 * @param int $number
 * @return string
 */
function html_signed_num($number) {
    if ($number > 0) {
        return "+{$number}";
    } else {
        return "{$number}";
    }
}

/**
 * Возвращает строку "positive" для положительных чисел,
 * "negative" для отрицательных и "zero" для ноля
 * @param int $number
 * @return string
 */
function html_signed_class($number) {
    if ($number > 0) {
        return 'positive text-success';
    } else if ($number < 0) {
        return 'negative text-danger';
    } else {
        return 'zero text-muted';
    }
}

/**
 * Возвращает скрытое поле, содержащее актуальный CSRF-токен
 * @return string
 */
function html_csrf_token() {
    return html_input('hidden', 'csrf_token', cmsForm::getCSRFToken());
}

/**
 * Возвращает число с числительным в нужном склонении
 *
 * @param int|float $num    Число, можно строкой
 * @param string|array $one Одно число или число|числа|чисел или ['число','числа','чисел']
 * @param ?string $two      Два числа
 * @param ?string $many     Много чисел
 * @param string $zero_text Нет чисел
 * @return string
 */
function html_spellcount($num, $one, $two = null, $many = null, $zero_text = LANG_NO) {

    $numeral = html_spellcount_only($num, $one, $two, $many);

    if (!$num) {
        return $zero_text . ' ' . $numeral;
    }

    return nf($num, 2, ' ') . ' ' . $numeral;
}

/**
 * Возвращает числительное в нужном склонении от переданного числа
 *
 * @param int|float $num    Число, можно строкой
 * @param string|array $one Одно число или число|числа|чисел или ['число','числа','чисел']
 * @param ?string $two      Два числа
 * @param ?string $many     Много чисел
 * @return string
 */
function html_spellcount_only($num, $one, $two = null, $many = null) {

    if (!$two || !$many) {
        [$one, $two, $many] = is_array($one) ? $one : explode('|', $one);
    }

    $num = (float)$num;

    if (floor($num) != $num) {
        return $two;
    }

    $mod10  = $num % 10;
    $mod100 = $num % 100;

    if ($mod10 === 1 && $mod100 !== 11) {
        return $one;
    }

    if ($mod10 >= 2 && $mod10 <= 4 && ($mod100 < 10 || $mod100 >= 20)) {
        return $two;
    }

    return $many;
}

/**
 * Возвращает отформатированный размер файла с единицей измерения
 *
 * @param integer $bytes
 * @param boolean $round
 * @return string
 */
function html_file_size($bytes, $round = false) {

    if (empty($bytes)) {
        return 0;
    }

    $s = [LANG_B, LANG_KB, LANG_MB, LANG_GB, LANG_TB, LANG_PB];
    $e = floor(log($bytes) / log(1024));

    $pattern = $round ? '%d' : '%.2f';

    $output = sprintf($pattern . ' ' . $s[$e], ($bytes / pow(1024, floor($e))));

    return $output;
}

/**
 * Форматирует число просмотров, сокращая большие числа с суффиксами 'K' и 'M'.
 *
 * Примеры:
 * - 0         → 0
 * - 999       → 999
 * - 1500      → 1.5K
 * - 2350000   → 2.35M
 *
 * @param int|float $num Количество просмотров
 * @return string Отформатированное число с сокращением
 */
function html_views_format($num) {

    if (!$num) { return '0'; }

    if ($num >= 1000000) {
        return nf($num / 1000000, 2, ' ') . 'M';
    }

    if ($num >= 1000) {
        return nf($num / 1000, 2, ' ') . 'K';
    }

    return (string) $num;
}

/**
 * Форматирует количество минут в строку с правильными склонениями: часы и минуты.
 *
 * Примеры:
 * - 0      →
 * - 45     → 45 минут
 * - 60     → 1 час
 * - 125    → 2 часа 5 минут
 *
 * @param int $minutes Количество минут (целое число 0 и больше)
 * @return string Отформатированная строка с учетом склонений
 */
function html_minutes_format($minutes) {

    $result = '';

    if ($minutes >= 60) {

        $hours   = intdiv($minutes, 60);
        $minutes = $minutes % 60;

        $result = html_spellcount($hours, LANG_HOUR1, LANG_HOUR2, LANG_HOUR10) ;
    }

    if ($minutes) {
        $result .= ($result ? ' ' : '') . html_spellcount($minutes, LANG_MINUTE1, LANG_MINUTE2, LANG_MINUTE10);
    }

    return $result;
}

/**
 * Возвращает склеенный в одну строку массив строк
 *
 * @param array $array
 * @return string
 */
function html_each($array) {
    return is_array($array) ? implode('', $array) : '';
}

/**
 * Вырезает из HTML-кода пробелы, табуляции и переносы строк
 *
 * @param string $html
 * @return string
 */
function html_minify(string $html) {

    $tag_pattern = '#<(textarea|pre|code)(\b[^>]*)>(.*?)</\1>#is';

    $preserved = [];
    $html = preg_replace_callback($tag_pattern, function($m) use (&$preserved) {
        $key = '___HTMLMIN_' . count($preserved) . '___';
        $preserved[$key] = $m[0];
        return $key;
    }, $html);

    $html = preg_replace([
        '/>(?=\S)/u',
        '/(?<=\S)</u',
        '/\s{2,}/u',
        '/[\r\n\t]+/u'
    ], [
        '> ',
        ' <',
        ' ',
        '',
    ], $html);

    if ($preserved) {
        return strtr($html, $preserved);
    }

    return $html;
}

/**
 * Форматирует число с группировкой классов многозначного числа
 *
 * @param string $number Число
 * @param integer $decimals Знаков после запятой
 * @param string $thousands_sep Разделитель тысяч
 * @param bool $trim_zero Обрезать нули
 * @return string
 */
function nf($number, $decimals = 2, $thousands_sep = '', $trim_zero = true) {

    $number = (float)str_replace(',', '.', (string)$number);

    $value = number_format($number, $decimals, '.', $thousands_sep);

    return ($trim_zero && $decimals)
        ? rtrim(rtrim($value, '0'), '.')
        : $value;
}
