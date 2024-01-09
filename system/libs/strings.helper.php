<?php

/**
 * Разбивает строку по разделителю, затем собирает обратно в CamelCase
 * Например "my_own_string" => "MyOwnString", разделитель "_"
 *
 * @param char $delimiter Разделитель
 * @param string $string Исходная строка
 * @return string
 */
function string_to_camel($delimiter, $string) {

    $result = '';
    $words  = explode($delimiter, mb_strtolower($string));

    foreach ($words as $word) {
        $result .= ucfirst($word);
    }

    return $result;
}

/**
 * Вырезает теги <br> из строки
 * @param string $string
 * @return string
 */
function string_strip_br($string) {
    return str_replace('<br>', '', str_replace('<br/>', '', $string));
}

/**
 * Возвращает значение языковой константы
 * Если константа не найдена, возвращает ее имя или значение по умолчанию
 *
 * Префикс LANG_ в имени константы можно не указывать
 * Регистр не имеет значения
 *
 * @param string $constant Название языковой константы
 * @param string $default
 * @return string
 */
function string_lang($constant, $default = false) {

    $constant = strtoupper($constant);

    if (!$default) { $default = $constant; }

    if (strpos($constant, 'LANG_') === false) {
        $constant = 'LANG_' . $constant;
    }

    if (defined($constant)) {
        $string = constant($constant);
    } else {
        $string = $default;
    }

    return $string;
}

/**
 * Преобразует строку с маской URL в обычное регулярное выражение
 *
 * Пример:
 *      "my*mask is %st place" => "my(.*)mask is ([0-9]+) place"
 *
 * @param string $mask
 * @return string
 */
function string_mask_to_regular($mask) {
    return str_replace([
        '%', '/', '*', '?', '{slug}'
    ], [
        '([0-9]+)', '\/', '(.*)', '\?', '([a-z0-9\-]*)'
    ], trim($mask));
}

/**
 * Разбивает текст на строки, а каждую строку на ID и VALUE, разделенные |,
 * формируя ассоциативный массив
 *
 * Пример входящей строки:
 *      "id1 | value1 \n id2 | value2"
 *
 * Пример результата:
 *      array('id1' => 'value1', 'id2' => 'value2')
 *
 * @param string $string_list
 * @return array
 */
function string_parse_list($string_list) {

    if (!$string_list) { return []; }

    $user = cmsUser::getInstance();

    $rows = explode("\n", $string_list);

    $list = [];

    foreach ($rows as $row) {

        if (!$row) { continue; }

        $row = trim($row);

        if (preg_match('/^{(.*)}$/i', $row, $matches)) {
            if (!$user->is_logged) { continue; }
            $row = trim($matches[1]);
        }

        if (!mb_strstr($row, '|')) {
            $list[] = ['value' => trim($row)];
        } else {
            list($id, $value) = explode('|', $row);
            $list[] = [
                'id'    => trim($id),
                'value' => trim($value)
            ];
        }
    }

    return $list;
}

function string_explode_list($string_list, $index_as_value = false) {

    $items = [];
    $rows  = explode("\n", trim($string_list));

    if (is_array($rows)) {
        foreach ($rows as $count => $row) {
            if (mb_strpos($row, '|')) {
                list($index, $value) = explode('|', trim($row));
            } else {
                $index = (string)($index_as_value ? $row : ($count + 1));
                $value = $row;
            }
            $items[trim($index)] = trim($value);
        }
    }

    return $items;
}

/**
 * Приводит ключи массива к строковому типу
 *
 * @param array $array Исходный массив
 * @return array
 */
function array_keys_to_string_type($array) {

    $keys        = array_keys($array);
    $values      = array_values($array);
    $string_keys = array_map('strval', $keys);

    return array_combine($string_keys, $values);
}

/**
 * Получает список аналогично string_parse_list() и ищет вхождение в него
 * заданной строки
 *
 * @param string $string
 * @param string $mask_list
 * @return boolean
 */
function string_in_mask_list($string, $mask_list) {

    if (!$mask_list) { return false; }

    $mask_list = explode("\n", $mask_list);

    foreach ($mask_list as $item) {

        $regular = string_mask_to_regular($item);
        $regular = "/^{$regular}$/iu";

        if (preg_match($regular, $string)) {
            return true;
        }
    }

    return false;
}

/**
 * Генерирует случайную последовательность символов заданной длины
 *
 * @param integer $length Длина последовательности
 * @param string $seed Соль
 * @return string
 */
function string_random($length = 32, $seed = '') {

    $salt = bin2hex(random_bytes(128));

    $string = md5(md5($salt . chr(random_int(0, 127)) . chr(random_int(0, 127))) . chr(random_int(0, 127)) . md5(md5($seed)));

    if ($length < 32) {
        $string = substr($string, 0, $length);
    }

    return $string;
}

/**
 * Выводит разницу между переданной датой и текущим временем
 * в виде читабельной строки со склонениями
 *
 * Пример вывода: "2 года 16 дней 5 часов 12 минут"
 *
 * @param string $date
 * @param array $options Массив элементов для перечисления: y, m, d, h, i, from_date
 * @param bool $is_add_back Добавлять к строке слово "назад"?
 * @return string
 */
function string_date_age($date, $options, $is_add_back = false) {

    if (!$date) { return ''; }

    $date2 = !empty($options['from_date']) ? $options['from_date'] : false;

    $diff = real_date_diff($date, $date2);

    $diff_str = [];

    if (in_array('y', $options) && $diff[0]) {
        $diff_str[] = html_spellcount($diff[0], LANG_YEAR1, LANG_YEAR2, LANG_YEAR10);
    }
    if (in_array('m', $options) && $diff[1]) {
        $diff_str[] = html_spellcount($diff[1], LANG_MONTH1, LANG_MONTH2, LANG_MONTH10);
    }
    if (in_array('d', $options) && $diff[2]) {
        $diff_str[] = html_spellcount($diff[2], LANG_DAY1, LANG_DAY2, LANG_DAY10);
    }
    if (in_array('h', $options) && $diff[3]) {
        $diff_str[] = html_spellcount($diff[3], LANG_HOUR1, LANG_HOUR2, LANG_HOUR10);
    }
    if (in_array('i', $options) && $diff[4]) {
        $diff_str[] = html_spellcount($diff[4], LANG_MINUTE1, LANG_MINUTE2, LANG_MINUTE10);
    }

    if (!$diff_str) {
        return LANG_SECONDS_AGO;
    } else {
        $diff_str = trim(implode(' ', $diff_str));
        return $is_add_back ? sprintf(LANG_DATE_AGO, $diff_str) : $diff_str;
    }
}

/**
 * Выводит максимальную разницу между переданной датой и текущим временем
 * в виде читабельной строки со склонением
 *
 * Пример вывода: "3 дня"
 *
 * @param string | array $date Дата или массив двух дат
 * @param bool $is_add_back Добавлять к строке слово "назад"?
 * @return string
 */
function string_date_age_max($date, $is_add_back = false) {

    if (!$date) { return ''; }

    if(is_array($date)){
        $diff = real_date_diff($date[0], $date[1]);
    } else {
        $diff = real_date_diff($date);
    }

    $diff_str = '';

    if ($diff[0]) {
        $diff_str = html_spellcount($diff[0], LANG_YEAR1, LANG_YEAR2, LANG_YEAR10);
    } else
    if ($diff[1]) {
        $diff_str = html_spellcount($diff[1], LANG_MONTH1, LANG_MONTH2, LANG_MONTH10);
    } else
    if ($diff[2]) {
        $diff_str = html_spellcount($diff[2], LANG_DAY1, LANG_DAY2, LANG_DAY10);
    } else
    if ($diff[3]) {
        $diff_str = html_spellcount($diff[3], LANG_HOUR1, LANG_HOUR2, LANG_HOUR10);
    } else
    if ($diff[4]) {
        $diff_str = html_spellcount($diff[4], LANG_MINUTE_1, LANG_MINUTE2, LANG_MINUTE10);
    }

    if (!$diff_str) {
        return LANG_JUST_NOW;
    } else {
        return $is_add_back ? sprintf(LANG_DATE_AGO, $diff_str) : $diff_str;
    }
}

/**
 * Возвращает разницу между датами в виде массива
 *
 * Возвращает массив, в котором элементы:
 *  0 => число лет
 *  1 => число месяцев
 *  2 => число дней
 *  3 => число часов
 *  4 => число минут
 *  5 => число секунд
 *
 * @author Олег Савватеев @ http://savvateev.org
 *
 * @param string $date1
 * @param string $date2
 * @return array
 */
function real_date_diff($date1, $date2 = null) {

    $diff = [];

    if (!is_string($date1)) { return false; }

    //Если вторая дата не задана принимаем ее как текущую
    if (!$date2) {
        $cd = getdate();
    } else {
        $cd = getdate(strtotime($date2));
    }

    $date2 = $cd['year'] . '-' . $cd['mon'] . '-' . $cd['mday'] . ' ' . $cd['hours'] . ':' . $cd['minutes'] . ':' . $cd['seconds'];

    //Преобразуем даты в массив
    $pattern = '/(\d+)\-(\d+)\-(\d+)(\s+(\d+)\:(\d+)\:(\d+))?/';
    preg_match($pattern, $date1, $matches);
    $d1 = [intval($matches[1]), intval($matches[2]), intval($matches[3]), intval($matches[5]), intval($matches[6]), intval($matches[7])];
    preg_match($pattern, $date2, $matches);
    $d2 = [intval($matches[1]), intval($matches[2]), intval($matches[3]), intval($matches[5]), intval($matches[6]), intval($matches[7])];

    //Если вторая дата меньше чем первая, меняем их местами
    for ($i = 0; $i < count($d2); $i++) {
        if ($d2[$i] > $d1[$i]) {
            break;
        }
        if ($d2[$i] < $d1[$i]) {
            $t  = $d1;
            $d1 = $d2;
            $d2 = $t;
            break;
        }
    }

    //Вычисляем разность между датами (как в столбик)
    $md1   = [31, $d1[0] % 4 || (!($d1[0] % 100) && $d1[0] % 400) ? 28 : 29, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31];
    $md2   = [31, $d2[0] % 4 || (!($d2[0] % 100) && $d2[0] % 400) ? 28 : 29, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31];
    $min_v = [NULL, 1, 1, 0, 0, 0];
    $max_v = [NULL, 12, $d2[1] == 1 ? $md2[11] : $md2[$d2[1] - 2], 23, 59, 59];
    for ($i = 5; $i >= 0; $i--) {
        if ($d2[$i] < $min_v[$i]) {
            $d2[$i - 1]--;
            $d2[$i] = $max_v[$i];
        }
        $diff[$i] = $d2[$i] - $d1[$i];
        if ($diff[$i] < 0) {
            $d2[$i - 1]--;
            $i == 2 ? $diff[$i] += $md1[$d1[1] - 1] : $diff[$i] += $max_v[$i] - $min_v[$i] + 1;
        }
    }

    //Возвращаем результат
    return $diff;
}

/**
 * Форматирует дату в формат "сегодня", "вчера", "1 января 2017"
 *
 * @param string $date Исходная дата. Может быть как отформатированном виде, так и timestamp
 * @param boolean $is_time Дополнять часом и минутами
 * @return string
 */
function string_date_format($date, $is_time = false) {

    if (is_empty_value($date)) {
        return '';
    }

    if (!is_numeric($date)) {
        $timestamp = strtotime($date);
    } else {
        $timestamp = $date;
    }

    $item_date = date('j F Y', $timestamp);

    $today_date     = date('j F Y');
    $yesterday_date = date('j F Y', time() - 3600 * 24);

    switch ($item_date) {
        case $today_date:
            $result = LANG_TODAY;
            break;
        case $yesterday_date:
            $result = LANG_YESTERDAY;
            break;
        default:
            $result = lang_date($item_date);
    }

    if ($is_time) {

        $result .= ' ' . LANG_IN . ' ' . date('H:i', $timestamp);
    }

    return $result;
}

/**
 * Находит в строке все выражения вида {file_name%icon_name} и заменяет на svg иконку
 * где file_name - имя svg файла по пути /templates/шаблон/images/icons/
 * icon_name - имя иконки svg спрайта
 *
 * @param string $string
 * @return string
 */
function string_replace_svg_icons($string){

    $matches_count = preg_match_all('/{([a-z0-9_\-]+)%([a-z0-9_\-]+)}/i', $string, $matches);

    if ($matches_count){
        for($i=0; $i<$matches_count; $i++){

            $tag  = $matches[0][$i];
            $file = $matches[1][$i];
            $name = $matches[2][$i];

            $string = str_replace($tag, html_svg_icon($file, $name, 16, false), $string);
        }
    }

    return $string;
}
/**
 * Находит в строке все выражения вида {user.property} и заменяет property
 * на соответствующее свойство объекта cmsUser
 *
 * @param string $string
 * @return string
 */
function string_replace_user_properties($string) {

    $matches_count = preg_match_all('/{user.([a-z0-9_]+)}/i', $string, $matches);

    if ($matches_count) {

        $user = cmsUser::getInstance();

        for ($i = 0; $i < $matches_count; $i++) {

            $tag      = $matches[0][$i];
            $property = $matches[1][$i];

            if (isset($user->$property)) {
                $string = str_replace($tag, $user->$property, $string);
            }
        }
    }

    return $string;
}

/**
 * Находит внутри строки $string все выражения вида {key}, где key - это ключ
 * массива $data и заменяет на значение соответствующего элемента
 *
 * @param string $string
 * @param array $data
 */
function string_replace_keys_values($string, $data) {

    if (!$string) {
        return '';
    }

    if (strpos($string, '{') === false) { return $string; }

    foreach ($data as $k => $v) {
        if (is_array($v) || is_object($v)) {
            unset($data[$k]);
        }
    }

    $keys = array_map(function ($key) {
        return '{' . $key . '}';
    }, array_keys($data));

    return str_replace($keys, array_values($data), $string);
}

/**
 * Находит внутри строки $string все выражения вида {key}, где key - это ключ
 * массива $data и заменяет на значение соответствующего элемента
 * отличительной особенностью от функции выше является возможность обработки значений функциями
 * например, выражение {age|html_spellcount:год:года:лет} после обработки напишет "21 год, 22 года, 29 лет"
 * при значении age 21, 22 и 29 соответственно
 * выражение {nickname:профиль пользователя %s самый лучший} после обработки станет "профиль пользователя Василий самый лучший"
 * при значении поля nickname в массиве $data "Василий"
 *
 * @param string $string
 * @param array $data
 */
function string_replace_keys_values_extended($string, $data) {

    if (!$string) {
        return '';
    }

    $matches_count = preg_match_all('/{([а-яёa-z]{1}[^}\n]+)}/ui', $string, $matches);

    if ($matches_count) {

        for ($i = 0; $i < $matches_count; $i++) {

            $tag      = $matches[0][$i];
            $property = $matches[1][$i];

            $func                 = false;
            $func_params          = [];
            $func_params_prop_key = 0;

            // есть ли обработка функцией
            if (strpos($property, '|') !== false) {
                $params = explode('|', $property);
                // второй параметр - функция
                $func   = $params[1];
                if (function_exists($func) || strpos($func, ':') !== false) {

                    // первый параметр остаётся как $property
                    $property = $params[0];
                    // $property ставим как первый параметр функции
                    $func_params = [$property];
                    // смотрим есть ли у функции параметры
                    if (strpos($func, ':') !== false) {
                        $par  = explode(':', $func);
                        $func = $par[0];
                        unset($par[0]);
                        if (function_exists($func)) {
                            foreach ($par as $k => $p) {
                                // если параметр - массив
                                if (strpos($p, '=') !== false) {
                                    $out = [];
                                    parse_str($p, $out);
                                    $par[$k] = $out;
                                }
                            }
                            $func_params = $func_params + $par;
                        } else {
                            $func = false;
                        }
                    }
                } else {

                    // значит рандомные значения из списка
                    $values = explode('|', $property);

                    $string = str_replace($tag, $values[mt_rand(0, (count($values) - 1))], $string);

                    continue;
                }
            } else
            // нужно прогнать через sprintf
            if (strpos($property, ':') !== false) {
                $params               = explode(':', $property);
                $property             = $params[0];
                $func                 = 'sprintf';
                $func_params          = array_reverse($params);
                $func_params_prop_key = 1;
            }

            // Поддерживаются вложенные элементы массива, т.е. ключи вида {item.key}
            if (strpos($property, '.') === false) {
                $data_property = isset($data[$property]) ? $data[$property] : null;
            } else {
                $data_property = array_value_recursive($property, $data, '.');
            }

            if ($data_property !== null && !is_array($data_property) && !is_object($data_property)) {

                if ($func && function_exists($func)) {

                    $func_params[$func_params_prop_key] = $data_property;

                    $data_property = call_user_func_array($func, $func_params);
                }

                $string = str_replace($tag, $data_property, $string);
            } else {
                $string = str_replace($tag, '', $string);
            }
        }
    }

    return $string;
}

/**
 * Делает активными гиперссылки внутри строки
 *
 * @param string $string
 * @return string
 */
function string_make_links($string){
    return preg_replace('@(https?:\/\/([\-\w\.]+[\-\w])+(:\d+)?(\/([\w/_\.#\-]*(\?\S+)?[^\.\s])?)?)@', '<a href="$1" class="auto-link" target="_blank" rel="noopener noreferrer">$1</a>', $string);
}

//============================================================================//

/**
 * Возвращает строку с перечислением самых часто используемых
 * слов из исходного текста
 *
 * @param string $text
 * @param int $min_length Минимальная длина каждого слова
 * @param int $limit Количество слов в результирующей строке
 * @return string
 */
function string_get_meta_keywords($text, $min_length = 5, $limit = 10) {

    if(!$text){ return ''; }

    $stat = [];

    $text = str_replace(["\n", '<br>', '<br/>'], ' ', $text);
    $text = strip_tags($text);
    $text = mb_strtolower($text);

    $stopwords = string_get_stopwords(cmsCore::getLanguageName());

    $words = explode(' ', $text);

    foreach ($words as $word) {

        $word = trim($word);
        $word = str_replace(['(', ')', '+', '-', '.', '!', ':', '{', '}', '|', '"', ',', "'"], '', $word);
        $word = preg_replace("/\.,\(\)\{\}/ui", '', $word);

        if ($stopwords && in_array($word, $stopwords)) {
            continue;
        }

        if (mb_strlen($word) >= $min_length) {
            $stat[$word] = isset($stat[$word]) ? $stat[$word] + 1 : 1;
        }
    }

    asort($stat);
    $stat = array_reverse($stat, true);
    $stat = array_slice($stat, 0, $limit, true);

    return implode(', ', array_keys($stat));
}

/**
 * Подготавливает текст для использования в теге meta description
 *
 * @param string $text
 * @param int $limit Максимальная длина результата
 * @return string
 */
function string_get_meta_description($text, $limit = 250) {
    return string_short($text, $limit);
}

/**
 * Возвращает массив стоп слов
 * @staticvar array $words
 * @param string $lang Язык, например ru, en
 * @return array
 */
function string_get_stopwords($lang = 'ru') {
    static $words = null;
    if (isset($words[$lang])) {
        return $words[$lang];
    }
    $file = PATH . '/system/languages/' . $lang . '/stopwords/stopwords.php';
    if (file_exists($file)) {
        $words[$lang] = include $file;
    } else {
        $words[$lang] = [];
    }
    return $words[$lang];
}

/**
 * Обрезает исходный текст до указанной длины (или последнего предложения/слова),
 * удаляя HTML-разметку
 *
 * @param string $string
 * @param integer $length Максимальная длина результата
 * @param string $postfix Строка, добавляемая к результату, если исходную пришлось обрезать
 * @param string $type Тип обрезки:
 *              s (sentence) - по последнему предложению
 *              w (word) - по последнему слову
 *              пустая строка или любой другой символ - обрезать в любом месте
 * @param array $clean_tags Массив тегов, которые надо вырезать вместе с содержимым перед очисткой
 * @return string
 */
function string_short($string, $length = 0, $postfix = '', $type = 's', $clean_tags = []) {

    if(!$string){ return ''; }

    if ($clean_tags) {
        $string = preg_replace('#<(' . implode('|', $clean_tags) . ')[^>]*?>.*</\\1>#sui', '', $string);
    }

    // строка может быть без переносов
    // и после strip_tags не будет пробелов между словами
    $string = str_replace(["\n", "\r", '<br>', '<br/>', '</p>'], ' ', $string);
    $string = strip_tags($string);
    $string = preg_replace('#\s\s+#u', ' ', $string);

    if (!$length || mb_strlen($string) <= $length) {
        return $string;
    }

    $length -= min($length, mb_strlen($postfix));

    switch (strtolower($type)) {
        // Обрезаем по последнему предложению
        case 's':
            $string = mb_substr($string, 0, $length);
            preg_match('/^(.+)([\.!?…]+)(.*)$/u', $string, $matches);
            if (!empty($matches[2])) {
                $string = $matches[1] . $matches[2];
            }
            break;
        // Обрезаем по последнему слову
        case 'w':
            $string = mb_substr($string, 0, $length + 1);
            preg_match('/^(.*)([\W]+)(\w*)$/uU', $string, $matches);
            if (!empty($matches[1])) {
                $string = $matches[1];
            }
            break;
        // Обрезаем как получится
        default:
            $string = mb_substr($string, 0, $length);
    }

    return $string . $postfix;
}

/**
 * Вырезает из строки CSS/JS-комментарии, табуляции, переносы строк и лишние пробелы
 *
 * @param string $string
 * @return string
 */
function string_compress($string) {

    $string = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $string);
    $string = preg_replace('/\s{2,}/', '', $string);
    $string = str_replace(["\r\n", "\r", "\n", "\t"], '', $string);

    return $string;
}

/**
 * Преобразует первый символ строки в верхний регистр
 * multi-bytes ucfirst
 *
 * @param string $string
 * @return string
 */
function string_ucfirst($string) {
    return mb_strtoupper(mb_substr($string, 0, 1)) . mb_substr($string, 1);
}

//============================================================================//

/**
 * Возвращает массив значений определенного поля для всех элементов коллекции
 *
 * Аналогично функции array_column из PHP 5.5
 *
 * @param type $collection
 * @param type $key
 * @param type $value
 * @return type
 */
function array_collection_to_list($collection, $key, $value = false) {

    $value = $value ? $value : $key;

    $list = [];

    if (is_array($collection)) {
        foreach ($collection as $item) {
            $list[$item[$key]] = $item[$value];
        }
    }

    return $list;
}

/**
 * Рекурсивная версия array_filter
 * @param array $input
 * @return array
 */
function array_filter_recursive($input) {
    foreach ($input as &$value) {
        if (is_array($value)) {
            $value = array_filter_recursive($value);
        }
    }
    return array_filter($input);
}

/**
 * Возвращает значение ячейки массива
 * по переданной вложенности $needle
 *
 * @param array|string $needle Путь до необходимого ключа, например key:subkey:subsubkey
 * @param array $haystack Массив, в котором ищем
 * @param string $delimiter Разделитель ключей в пути, если $needle строка
 * @return mixed Значение или null, если ключ не найден
 */
function array_value_recursive($needle, $haystack, $delimiter = ':') {

    if (!is_array($haystack)) { return null; }

    $name_parts = !is_array($needle) ? explode($delimiter, $needle) : $needle;

    foreach ($name_parts as $name) {
        if (!is_array($haystack) || !array_key_exists($name, $haystack)) {
            return null;
        } else {
            $haystack = $haystack[$name];
            if ($haystack === null) {
                $haystack = false;
            }
        }
    }

    return $haystack;
}

/**
 * Устанавливает значение ключа массив
 * по переданной вложенности ключей $path
 *
 * @param array|string $path Путь до необходимого ключа, например key:subkey:subsubkey
 * @param array $array Изменяемый массив
 * @param mixed $value Значение ключа
 * @param string $delimiter Разделитель ключей в пути, если $path строка
 * @return mixed Возвращает изменённый массив $array
 */
function set_array_value_recursive($path, $array, $value, $delimiter = ':') {

    $name_parts = !is_array($path) ? explode($delimiter, $path) : $path;

    $_array = &$array;

    foreach ($name_parts as $name) {
        $_array = &$_array[$name];
    }

    $_array = $value;

    return $array;
}

/**
 * Сортирует двумерный ассоциативный массив по полю (полям)
 *
 * $fields может содержать как просто имя поля для сортировки,
 * так и массив полей с направлениями сортировок, например:
 * array(array('by' => 'ordering', 'to' => 'asc'), array('by' => 'title', 'to' => 'desc'))
 *
 * @param array &$array
 * @param string | array $fields
 * @param string $direction
 * @return boolean
 */
function array_order_by(&$array, $fields, $direction = 'asc') {

    if (!$array) { return false; }

    if (is_string($fields)) {
        $list = [[
            'by' => $fields,
            'to' => $direction
        ]];
    } else {
        $list = $fields;
    }

    $args = [];

    foreach ($array as $k => $item) {

        $key = 0;

        foreach ($list as $order) {
            $args[$key][$k] = $item[$order['by']];
            $key++;
            $args[$key] = constant('SORT_' . strtoupper($order['to']));
            $key++;
        }
    }

    $args[] = &$array;

    return call_user_func_array('array_multisort', $args);
}

function multi_array_unique($array) {

    $result = array_map('unserialize', array_unique(array_map('serialize', $array)));

    foreach ($result as $key => $value) {
        if (is_array($value)) {
            $result[$key] = multi_array_unique($value);
        }
    }

    return $result;
}

/**
 * Возвращает значение поля с учётом языка
 *
 * @param string $field Название поля без языкового префикса
 * @param array $data Массив данных
 * @return mixed
 */
function get_localized_value($field, $data) {

    $lang = cmsCore::getLanguageHrefPrefix();

    $field_lang = $field . ($lang ? '_' . $lang : '');

    // Есть переведённое
    if (array_key_exists($field_lang, $data)) {
        return $data[$field_lang];
    }

    // Есть без перевода
    if (array_key_exists($field, $data)) {
        return $data[$field];
    }

    return null;
}

/**
 * Форматирует число с плавающей точкой
 *
 * @param float $value Число с плавающей точкой
 * @param integer $decimals Кол-во знаков после запятой
 * @param integer $thousands_sep Разделитель тысячей
 * @return string
 */
function nf_amount($value, $decimals = 2, $thousands_sep = ' ') {

    $value = number_format(floatval($value), $decimals, '.', $thousands_sep);

    return strpos($value, '.') !== false ? rtrim(rtrim($value, '0'), '.') : $value;
}

/**
 * Приводит число с плавающей точкой к нормальному виду
 *
 * @param float $value
 * @return string
 */
function bc_format($value) {
    return sprintf('%.'.BCMATHSCALE.'f', $value);
}

/**
 * Преобразует ipv4/ipv6 адрес в
 * упакованный формат для хранения
 *
 * @param string $ip
 * @return string
 */
function string_iptobin($ip) {
    if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
        return current(unpack('A4', inet_pton($ip)));
    } elseif (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
        return current(unpack('A16', inet_pton($ip)));
    } else {
        return null;
    }
}

/**
 * Преобразует упакованный ipv4/ipv6 адрес в
 * читаемый формат
 *
 * @param string $str
 * @return string
 */
function string_bintoip($str) {
    if(!$str){ return null; }
    $len = strlen($str);
    if($len < 4){ $len = 4; }
    if ($len === 16 || $len === 4) {
        return inet_ntop(pack('A' . $len, $str));
    } else {
        return null;
    }
}

/**
 * Определяет локацию по ip адресу
 *
 * @param string $ip
 * @param boolean $return_array
 * @return string|array
 */
function string_ip_to_location($ip, $return_array = false) {

    // Старая база
    // Теоретически можно подключить https://github.com/maxmind/GeoIP2-php
    if(function_exists('geoip_record_by_name')){

        $location  = [];

        $data = geoip_record_by_name($ip);

        if($return_array && !empty($data['country_code'])){
            $location['code'] = $data['country_code'];
        }

        if(!empty($data['country_name'])){
            $location['country'] = $data['country_name'];
        }

        if(!empty($data['city'])){
            $location['city'] = $data['city'];
        }

        return $return_array ? $location : implode(', ', $location);
    }

    return $return_array ? [] : '';
}

/**
 * Получает из HTML текста относительные пути
 * из тегов <img>
 *
 * @param string $text
 * @return array
 */
function string_html_get_images_path($text) {

    $upload_root = cmsConfig::get('upload_root');

    $matches = $paths = [];

    preg_match_all('#<img[^>]+src="?\'?([^"\']+)"?\'?[^>]*#uis', $text, $matches, PREG_SET_ORDER);

    if($matches){
        foreach($matches as $match){

            if(empty($match[1])){ continue; }
            if(strpos($match[1], 'http') === 0){ continue; }

            $path = $match[1];

            if(strpos($path, $upload_root) === 0){
                $path = str_replace($upload_root, '', $path);
            }

            $paths[] = $path;
        }
    }

    return $paths;
}

/**
 * Кодирует символы для использования в части URL
 *
 * @param string $str
 * @return string
 */
function string_urlencode($str) {

    $str = urlencode($str);

    return str_replace(['%2F'], ['%252f'], $str);
}

/**
 * Проверяет, что значение пустое
 * @param mixed $value
 * @return boolean
 */
function is_empty_value($value) {
    return empty($value) && !is_numeric($value);
}
//============================================================================//

/**
 * Выводит переменную рекурсивно
 * Используется для отладки
 *
 * @param mixed $var
 * @param boolean $halt
*/
function dump($var, $halt = true) {
    echo '<pre>';
    print_r($var);
    echo '</pre>';
    if ($halt) { die(); }
}
