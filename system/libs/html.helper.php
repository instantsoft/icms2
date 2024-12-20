<?php

/**
 * Выводит строку, безопасную для html
 *
 * @param string  $string Строка
 * @param boolean $print Печатать результат или возвращать, по умолчанию true
 */
function html(string $string, bool $print = true) {

    // Должна быть строка
    $string = htmlentities($string, ENT_QUOTES | ENT_HTML401, 'UTF-8');

    if (!$print) {
        return $string;
    }

    echo $string;
}

/**
 * Очищает строку от тегов и обрезает до нужной длины
 *
 * @param string  $string Строка
 * @param integer $max_length Максимальное кол-во символов, по умолчанию false
 *
 * @return string
 */
function html_clean(string $string, $max_length = false): string
{

    // строка может быть без переносов
    // и после strip_tags не будет пробелов между словами
    $string = str_replace(["\n", "\r", '<br>', '<br/>'], ' ', $string);
    $string = strip_tags($string);

    if ($max_length) {
        $string = html_strip($string, $max_length);
    }

    return $string;
}

/**
 * Обрезает строку до заданного кол-ва символов
 *
 * @param string  $string Строка
 * @param integer $max_length Кол-во символов, которые нужно оставить от начала строки
 *
 * @return string
 */
function html_strip(string $string, int $max_length): string
{

    $length = mb_strlen($string);

    if ($length > $max_length) {
        $string = mb_substr($string, 0, (int)$max_length);
        $string .= '...';
    }

    return $string;
}

/**
 * Формирует ссылку по относительной (без добавления корня URL)
 *
 * @param string  $rel_link
 * @param boolean $is_abs
 *
 * @return string
 */
function rel_to_href(string $rel_link, bool $is_abs = false): string
{

    $lang_href = cmsCore::getLanguageHrefPrefix();

    return ($is_abs ? cmsConfig::get('host') . '/' : cmsConfig::get('root')) . ($lang_href ? $lang_href . '/' : '') . $rel_link;
}

/**
 * Возвращает ссылку на профиль пользователя
 *
 * @param mixed   $user Массив данных пользователя
 * @param mixed   $params
 * @param boolean $is_abs
 *
 * @return string
 */
function href_to_profile($user, $params = false, bool $is_abs = false): string
{

    $href_func = 'href_to';
    if($is_abs){
        $href_func = 'href_to_abs';
    }

    if (is_array($user)) {
        return $href_func('users', (empty($user['slug']) ? $user['id'] : $user['slug']), $params);
    }

    if(is_object($user)) {
        return $href_func('users', (empty($user->slug) ? $user->id : $user->slug), $params);
    }

    return $href_func('users', $user, $params);
}

/**
 * Возвращает ссылку на указанное действие контроллера
 * с добавлением пути от корня сайта
 *
 * @param string               $controller Имя контроллера
 * @param string               $action Имя экшена
 * @param array|string|int $params Параметры экшена
 * @param array                $query Параметры строки запроса
 *
 * @return string
 */
function href_to(string $controller, string $action = '', $params = false, array $query = []): string
{

    $lang_href = cmsCore::getLanguageHrefPrefix();

    return cmsConfig::get('root') .($lang_href ? $lang_href.'/' : ''). href_to_rel($controller, $action, $params, $query);
}

/**
 * Возвращает ссылку на указанное действие контроллера
 * с добавлением хоста сайта
 *
 * @param string           $controller
 * @param string           $action
 * @param array|string|int $params Параметры, массив
 *
 * @return string
 */
function href_to_abs(string $controller, string $action = '', $params = false, $query = []): string
{

    $lang_href = cmsCore::getLanguageHrefPrefix();

    return cmsConfig::get('host') . '/' .($lang_href ? $lang_href.'/' : ''). href_to_rel($controller, $action, $params, $query);
}

/**
 * Возвращает ссылку на указанное действие контроллера без добавления корня URL
 *
 * @param string               $controller Имя контроллера
 * @param string               $action Имя действия
 * @param array|string|integer $params Параметры, массив
 * @param array                $query Параметры строки запроса
 *
 * @return string
 */
function href_to_rel(string $controller, string $action = '', $params = false, array $query = []): string
{

    $controller = trim($controller, '/ ');

    $ctype_default = cmsConfig::get('ctype_default');

    if ($ctype_default && $action && in_array($controller, $ctype_default)) {
        if (preg_match('/[a-z0-9\-\/{}]+(\.html|\/view\-[a-z0-9\-_]+)$/i', $action)) {
            $controller = '';
        }
    }

    
    if (($controller_alias = cmsCore::getControllerAliasByName($controller)) !== false) {
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
 *
 * @return string
 */
function href_to_current(bool $add_host = false): ?string
{

    static $rel_url = null;

    if ($rel_url === null) {

        $lang_href = cmsCore::getLanguageHrefPrefix();
        $lang_href = ($lang_href ? '/' . $lang_href : '');

        $rel_url = $lang_href . cmsCore::getInstance()->request->getServer('REQUEST_URI');
    }

    if ($add_host) {
        return cmsConfig::get('host') . $rel_url;
    }

    return $rel_url;
}

/**
 * Возвращает ссылку на главную страницу сайта
 *
 * @param bool $add_host Добавлять http://ваш-сайт.ру
 *
 * @return string
 */
function href_to_home(bool $add_host = false): string
{
    return ($add_host ? cmsConfig::get('host').'/' : cmsConfig::get('root')).cmsCore::getLanguageHrefPrefix();
}

/**
 * Возвращает отформатированную строку аттрибутов тега
 *
 * @param array   $attributes Атрибуты тега название=>значение
 * @param boolean $unset_class_key Не формировать CSS класс
 *
 * @return string
 */
function html_attr_str(array $attributes, bool $unset_class_key = true): string
{

    if (!$attributes) {
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
            $attr_parts[] = $key . '="' . html($val, false) . '"';
        }
    }

    return implode(' ', $attr_parts) . ' ';
}

/**
 * Печатает короткий HTML тег
 *
 * @param string $tag_name Имя тега
 * @param array  $attributes Атрибуты тега название=>значение
 * @param string $class CSS класс, если он отдельно от атрибутов
 *
 * @return string
 */
function html_tag_short(string $tag_name, array $attributes, string $class = ''): string
{

    if(!empty($attributes['class'])){
        $class .= ' ' . $attributes['class'];
    }
    $attributes['class'] = trim($class);

    return '<'.$tag_name.' ' . html_attr_str($attributes, false) . '/>';
}

function default_images($type, $preset): array
{
    return [
        $preset => 'default/' . $type . '_' . $preset . '.png'
    ];
}

/**
 * Возвращает ссылку на аватар пользователя
 *
 * @param array|string  $avatars Все изображения аватара
 * @param string $size_preset Название пресета
 *
 * @return string
 */
function html_avatar_image_src($avatars, string $size_preset = 'small', $is_relative = true): ?string
{

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
 *
 * @param array|string $image Все размеры заданного изображения
 * @param string       $size_preset Название пресета
 * @param bool         $is_add_host Возвращать путь отностительно директории хранения или полный путь
 * @param bool         $is_relative Возвращать относительный путь или всегда с полным url
 *
 * @return boolean|string
 */
function html_image_src($image, string $size_preset = 'small', bool $is_add_host = false, bool $is_relative = true) {

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
 * @param array  $config Параметры редактора
 *
 * @return string HTML код
 */
function html_wysiwyg(string $field_name, string $content = '', $wysiwyg = false, array $config = []): string
{

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

    [$field_name, $content, $wysiwyg, $config] = cmsEventsManager::hook(['display_wysiwyg_editor', 'display_' . $wysiwyg . '_wysiwyg_editor'], [$field_name, $content, $wysiwyg, $config]);

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
 * @param array  $options
 *
 * @return string
 */
function html_editor(string $field_name, string $content = '', array $options = []): string
{
    return html_wysiwyg($field_name, $content, 'markitup', $options);
}

function html_select_range($name, $start, $end, $step, $add_lead_zero=false, $selected='', $attributes=[]): string
{

    $items = [];

    for($i=$start; $i<=$end; $i+=$step){
        if ($add_lead_zero){
            $i = $i > 9 ? $i : "0{$i}";
        }
        $items[$i] = $i;
    }

    return html_select($name, $items, $selected, $attributes);

}

/**
 * Возвращает строку содержащую число со знаком плюс или минус
 *
 * @param int $number
 *
 * @return string
 */
function html_signed_num(int $number): string
{
    return ($number >= 0 ? '+' : '') . $number;
}

/**
 * Возвращает строку "positive" для положительных чисел,
 * "negative" для отрицательных и "zero" для ноля
 *
 * @param int $number
 *
 * @return string
 */
function html_signed_class(int $number): string
{
    if ($number > 0){
        return 'positive text-success';
    }

    if ($number < 0){
        return 'negative text-danger';
    }

    return 'zero text-muted';
}

/**
 * Возвращает скрытое поле, содержащее актуальный CSRF-токен
 * @return string
 */
function html_csrf_token(): string
{
    return html_input('hidden', 'csrf_token', cmsForm::getCSRFToken());
}

/**
 * Возвращает число с числительным в нужном склонении
 *
 * @param int    $num
 * @param string $one
 * @param string $two
 * @param string $many
 * @param string $zero_text
 *
 * @return string
 */
function html_spellcount(int $num, string $one, $two = false, $many = false, string $zero_text = LANG_NO): string
{

    if (!$two && !$many){
        [$one, $two, $many] = explode('|', $one);
    }

    if (!$num){
        return $zero_text.' '.$many;
    }

    return nf($num, 0, ' ').' '.html_spellcount_only($num, $one, $two, $many);
}

function html_spellcount_only($num, $one, $two = false, $many = false) {

    if (!$two && !$many) {
        [$one, $two, $many] = explode('|', $one);
    }

    if (strpos((string)$num, '.') !== false) {
        return $two;
    }

    $n10 = $num % 10;
    $n100 = $num % 100;

    if ($n10 == 1 && $n100 != 11) {
        return $one;
    }

    if ($n10 >= 2 && $n10 <= 4 && ($n100 < 10 || $n100 >= 20)) {
        return $two;
    }

    return $many;
}

/**
 * Возвращает отформатированный размер файла с единицей измерения
 *
 * @param int $bytes
 * @param boolean $round
 *
 * @return string
 */
function html_file_size(int $bytes, bool $round = false): string {

    if (empty($bytes)) {
        return '0';
    }

    $s = [LANG_B, LANG_KB, LANG_MB, LANG_GB, LANG_TB, LANG_PB];
    $e = floor(log($bytes) / log(1024));

    $pattern = $round ? '%d' : '%.2f';

    return sprintf($pattern . ' ' . $s[$e], ($bytes / (1024 ** floor($e))));
}

function html_views_format($num): string
{

    if(!$num) { return '0'; }

    if($num >= 1000000){
        return nf($num/1000000, 2, ' ').'M';
    }

    if($num >= 1000){
        return nf($num/1000, 2, ' ').'K';
    }

    return (string)$num;
}

function html_minutes_format($minutes): string
{

    if(!$minutes) { return ''; }

    if($minutes >= 60){

        $hours = floor($minutes / 60);
        $min = $minutes - ($hours * 60);

        return html_spellcount($hours, LANG_HOUR1, LANG_HOUR2, LANG_HOUR10).($min ? ' '.html_spellcount($min, LANG_MINUTE1, LANG_MINUTE2, LANG_MINUTE10) : '');
    }

    return html_spellcount($minutes, LANG_MINUTE1, LANG_MINUTE2, LANG_MINUTE10);
}

/**
 * Возвращает склеенный в одну строку массив строк
 *
 * @param array|string $value
 *
 * @return string
 */
function html_each($value): string
{

    $result = '';

    if (is_array($value)) {
        $result = implode('', $value);
    }

    return $result;
}

/**
 * Вырезает из HTML-кода пробелы, табуляции и переносы строк
 *
 * @param string $html
 *
 * @return string
 */
function html_minify(string $html): string
{
    return preg_replace([
        '/\>[^\S ]+/us',
        '/[^\S ]+\</us',
        '/(\s)+/us'
    ], [
        '>',
        '<',
        '\\1'
    ], $html);
}

/**
 *
 * @param float   $number Число
 * @param integer $decimals Знаков после запятой
 * @param string  $thousands_sep Разделитель тысяч
 *
 * @return string
 */
function nf(float $number, int $decimals = 2, string $thousands_sep = ''): string
{
    if (!$number) { return '0'; }
    $value = number_format((double) str_replace(',', '.', $number), $decimals, '.', $thousands_sep);
    if($decimals){
        return rtrim(rtrim($value, '0'), '.');
    }
    return $value;
}
