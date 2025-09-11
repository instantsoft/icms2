<?php

/**
 * Разбивает строку по разделителю, затем собирает обратно в CamelCase
 * Например "my_own_string" => "MyOwnString", разделитель "_"
 *
 * @param string $delimiter Разделитель
 * @param string $string Исходная строка
 * @return string
 */
function string_to_camel(string $delimiter, string $string) {
    return str_replace(' ', '', ucwords(str_replace($delimiter, ' ', strtolower($string))));
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

    $constant = (strpos($constant, 'LANG_') === 0) ? $constant : 'LANG_' . $constant;

    return defined($constant) ? constant($constant) : ($default ?: $constant);
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

    $list = [];

    if (!$string_list) { return $list; }

    $is_logged = cmsUser::isLogged();

    $rows = explode("\n", $string_list);

    foreach ($rows as $row) {

        if (preg_match('/^{(.*)}/', $row, $matches)) {
            if (!$is_logged) { continue; }
            $row = $matches[1];
        }

        $parts = array_map('trim', explode('|', $row, 2));

        $list[] = isset($parts[1])
            ? ['id' => $parts[0], 'value' => $parts[1]]
            : ['value' => $parts[0]];
    }

    return $list;
}

/**
 * Разбивает текст на строки, где ключ и значение разделены |, создавая ассоциативный массив
 *
 * @param string $string_list   Исходная строка для разбивки
 * @param bool $index_as_value  Определяет, использовать ли строку как ключ, если | отсутствует
 * @return array                Ассоциативный массив с ключами и значениями
 */
function string_explode_list($string_list, $index_as_value = false) {

    $items = [];

    $rows = explode("\n", $string_list);

    foreach ($rows as $count => $row) {

        $parts = array_map('trim', explode('|', $row, 2));

        if (isset($parts[1])) {
            $index = $parts[0];
            $value = $parts[1];
        } else {
            $index = $index_as_value ? $parts[0] : (string)($count + 1);
            $value = $parts[0];
        }

        $items[$index] = $value;
    }

    return $items;
}

/**
 * Приводит ключи массива к строковому типу
 *
 * @param array $array Исходный массив
 * @return array
 */
function array_keys_to_string_type(array $array) {

    $keys        = array_keys($array);
    $values      = array_values($array);
    $string_keys = array_map('strval', $keys);

    return array_combine($string_keys, $values);
}

/**
 * Получает список из строки, разбивая по \n и ищет
 * вхождение в него заданной строки
 *
 * @param string $string
 * @param string $mask_list
 * @return boolean
 */
function string_in_mask_list($string, $mask_list) {

    if (!$mask_list) {
        return false;
    }

    return string_matches_mask_list(explode("\n", $mask_list), $string);
}

/**
 * Проверяет, совпадает ли хотя бы одна маска со строкой
 *
 * @param array $masks Массив масок
 * @param string $string
 * @return bool
 */
function string_matches_mask_list(array $masks, string $string) {

    foreach ($masks as $mask) {
        $regular = string_mask_to_regular($mask);
        if (preg_match("/^{$regular}$/iu", $string)) {
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

    $salt = bin2hex(random_bytes(32));

    $string = md5($salt . $seed . random_bytes(16));

    return ($length < 32) ? substr($string, 0, $length) : $string;
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

    if (!$date) {
        return '';
    }

    // Определяем дату для сравнения
    $from_date = $options['from_date'] ?? false;

    // Вычисляем разницу между датами
    $diff = real_date_diff($date, $from_date);

    // Соответствие ключей и языковых констант для склонений
    $mapping = [
        'y' => [$diff[0], LANG_YEAR1, LANG_YEAR2, LANG_YEAR10],
        'm' => [$diff[1], LANG_MONTH1, LANG_MONTH2, LANG_MONTH10],
        'd' => [$diff[2], LANG_DAY1, LANG_DAY2, LANG_DAY10],
        'h' => [$diff[3], LANG_HOUR1, LANG_HOUR2, LANG_HOUR10],
        'i' => [$diff[4], LANG_MINUTE1, LANG_MINUTE2, LANG_MINUTE10]
    ];

    $diff_parts = [];

    foreach ($mapping as $key => $map_value) {

        list($value, $one, $two, $many) = $map_value;

        if (in_array($key, $options, true) && $value) {
            $diff_parts[] = html_spellcount($value, $one, $two, $many);
        }
    }

    if (!$diff_parts) {
        return LANG_SECONDS_AGO;
    }

    // Формируем финальную строку
    $diff_str = implode(' ', $diff_parts);

    return $is_add_back ? sprintf(LANG_DATE_AGO, $diff_str) : $diff_str;
}

/**
 * Выводит максимальную разницу между переданной датой и текущим временем
 * в виде читабельной строки со склонением
 *
 * Пример вывода: "3 дня"
 *
 * @param string|array $date Дата или массив двух дат
 * @param bool $is_add_back Добавлять к строке слово "назад"?
 * @return string
 */
function string_date_age_max($date, $is_add_back = false) {

    if (!$date) {
        return '';
    }

    // Вычисляем разницу, в зависимости от того, передан массив дат или одна дата
    $diff = is_array($date) ? real_date_diff($date[0], $date[1]) : real_date_diff($date);

    $mapping = [
        0 => [LANG_YEAR1, LANG_YEAR2, LANG_YEAR10],
        1 => [LANG_MONTH1, LANG_MONTH2, LANG_MONTH10],
        2 => [LANG_DAY1, LANG_DAY2, LANG_DAY10],
        3 => [LANG_HOUR1, LANG_HOUR2, LANG_HOUR10],
        4 => [LANG_MINUTE_1, LANG_MINUTE2, LANG_MINUTE10]
    ];

    // Перебираем разницу по убыванию, начиная с лет и заканчивая минутами
    foreach ($mapping as $index => $map_value) {

        list($one, $two, $many) = $map_value;

        if (!empty($diff[$index])) {

            $diff_str = html_spellcount($diff[$index], $one, $two, $many);

            return $is_add_back ? sprintf(LANG_DATE_AGO, $diff_str) : $diff_str;
        }
    }

    // Если ни одна разница не нашлась, возвращаем "только что"
    return LANG_JUST_NOW;
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
 * @param string $date1
 * @param ?string $date2
 * @return array
 */
function real_date_diff($date1, $date2 = null) {

    $default = [0, 0, 0, 0, 0, 0];

    // Проверяем, что $date1 это строка
    if (!is_string($date1)) {
        return $default;
    }

    try {

        $datetime1 = new DateTime($date1);
        // Если вторая дата не указана, берем текущую
        $datetime2 = $date2 ? new DateTime($date2) : new DateTime();

        // Вычисляем разницу между датами в виде объекта DateInterval
        $interval = $datetime1->diff($datetime2);

        return [
            $interval->y,  // Лет
            $interval->m,  // Месяцев
            $interval->d,  // Дней
            $interval->h,  // Часов
            $interval->i,  // Минут
            $interval->s   // Секунд
        ];
    } catch (Exception $e) {
        return $default;
    }
}

/**
 * Форматирует дату в формат "сегодня", "вчера", "1 января 2017"
 *
 * @param string $date Исходная дата. Может быть как отформатированном виде, так и timestamp
 * @param bool $is_time Дополнять часом и минутами
 * @return string
 */
function string_date_format($date, $is_time = false) {

    if (is_empty_value($date)) {
        return '';
    }

    $timestamp = is_numeric($date) ? (int) $date : strtotime($date);

    $today     = strtotime('today');
    $yesterday = strtotime('yesterday');

    if ($timestamp >= $today) {
        $result = LANG_TODAY;
    } elseif ($timestamp >= $yesterday) {
        $result = LANG_YESTERDAY;
    } else {
        $result = lang_date(date('j F Y', $timestamp));
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
 * @param string $string Строка для поиска
 * @return string
 */
function string_replace_svg_icons($string) {

    return preg_replace_callback(
        '/{([a-z0-9_\-]+)%([a-z0-9_\-]+)}/i',
        function ($matches) {
            return html_svg_icon($matches[1], $matches[2], 16, false);
        },
        $string
    );
}

/**
 * Находит в строке все выражения вида {user.property} и заменяет property
 * на соответствующее свойство объекта cmsUser
 *
 * @param string $string Строка для поиска
 * @param ?cmsUser $user Объект cmsUser
 * @return string
 */
function string_replace_user_properties($string, $user = null) {

    if (!$user) {
        $user = cmsUser::getInstance();
    }

    return preg_replace_callback(
        '/{user\.([a-z0-9_]+)}/i',
        function ($matches) use ($user) {
            return $user->{$matches[1]} ?? $matches[0];
        },
        $string
    );
}

/**
 * Находит внутри строки $string все выражения вида {key}, где key - это ключ
 * массива $data и заменяет на значение соответствующего элемента
 *
 * @param string $string
 * @param array $data
 */
function string_replace_keys_values($string, $data) {

    if (!$string || strpos($string, '{') === false) {
        return $string;
    }

    // Фильтруем массив $data, удаляя массивы и объекты
    $filtered_data = array_filter($data, function($v) {
        return !is_array($v) && !is_object($v);
    });

    // Создаём массив ключей вида {key}
    $keys = array_map(function ($key) {
        return '{' . $key . '}';
    }, array_keys($filtered_data));

    return str_replace($keys, array_values($filtered_data), $string);
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
 * @param string $string           Строка для поиска
 * @param array $data              Массив данных для замены
 * @param bool $keep_not_found_key Если в $data нет ключа key, оставлять выражение {key} неизменным
 */
function string_replace_keys_values_extended($string, $data, $keep_not_found_key = false) {

    $escape = function($str, $is_flip = true) {

        // Массив замен для экранированных символов
        $from = ['\?' => '{{Q}}', '\:' => '{{C}}', '\|' => '{{P}}', '\=' => '{{E}}'];
        $to   = ['{{Q}}' => '?', '{{C}}' => ':', '{{P}}' => '|', '{{E}}' => '='];

        return $is_flip ? strtr($str, $to) : strtr($str, $from);
    };

    return preg_replace_callback('/{([\w]{1}[^}\n]+)}/ui', function ($matches) use ($data, $escape, $keep_not_found_key) {

        $expression = $escape($matches[1], false);

        $has_pipeline  = strpos($expression, '|') !== false;
        $has_condition = strpos($expression, '?') !== false;
        $has_colon     = strpos($expression, ':') !== false;

        // Обрабатываем условия
        if ($has_condition) {

            list($key, $condition) = explode('?', $expression, 2);

            $options = explode('|', $condition, 2);

            if (strpos($key, '=') !== false) {

                list($key, $compare_value) = explode('=', $key, 2);

                $value = array_value_recursive($key, $data, '.');

                $result = ($value == $compare_value) ? $options[0] : ($options[1] ?? '');

            } else {

                $value = array_value_recursive($key, $data, '.');

                $result = $value ? $options[0] : ($options[1] ?? '');
            }

            return $escape(sprintf($result, $value));
        }

        // Разбираем выражение (ключ | функция:параметры)
        $func   = null;
        $params = [];
        $add_value_func = 'array_unshift';

        // Передана функция
        if ($has_pipeline) {

            $options = explode('|', $expression);

            list($key, $func) = $options;

            $params = explode(':', $func);
            $func   = array_shift($params);

            // Рандомный список
            if (!function_exists($func)) {
                return $escape($options[array_rand($options)]);
            }

        // Функция sprintf
        } elseif ($has_colon) {

            $params = explode(':', $expression, 2);
            $key    = array_shift($params);
            $func   = 'sprintf';
            $add_value_func = 'array_push';

        // Просто ключ
        } else {
            $key = $expression;
        }

        // Получаем значение из массива
        $value = array_value_recursive($key, $data, '.');

        if ($value === false || $value === null || !$func) {

            if ($keep_not_found_key && $value === null) {
                return $matches[0];
            }

            return (string) $value;
        }

        // Обрабатываем параметры функции
        foreach ($params as &$param) {
            if (strpos($param, '=') !== false) {
                parse_str($param, $parsed);
                $param = $parsed;
            }
        }

        $add_value_func($params, $value);

        return $escape(call_user_func_array($func, $params));

    }, (string) $string);
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
    $string = strip_tags(str_ireplace(['<br>', '<br/>', '</p>', "\n", "\r"], ' ', $string));
    $string = preg_replace('/\s{2,}/u', ' ', $string);

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
 * Возвращает значение поля с учётом языка или иного постфикса
 *
 * @param string $field_name Название поля без языкового префикса
 * @param array $data Массив данных
 * @param ?string $lang Язык или вариативный постфикс поля
 * @return mixed
 */
function get_localized_value($field_name, $data, $lang = null) {

    if (!$lang) {
        $lang = cmsCore::getLanguageHrefPrefix();
    }

    $field_name_lang = $field_name . ($lang ? '_' . $lang : '');

    // Есть переведённое
    if (array_key_exists($field_name_lang, $data)) {
        return $data[$field_name_lang];
    }

    // Есть без перевода
    if (array_key_exists($field_name, $data)) {
        return $data[$field_name];
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
 * Функция аналогична str_replace, только заменяет
 * первое вхождение и не принимает массивы
 *
 * @param string $search Что ищем
 * @param string $replace На что меняем
 * @param string $subject Где ищем
 * @return string
 */
function string_replace_first($search, $replace, $subject) {

    $pos = strpos($subject, $search);

    if ($pos !== false) {
        return substr_replace($subject, $replace, $pos, strlen($search));
    }

    return $subject;
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
/**
 * Если авторизация под админом
 * Выводит переменную рекурсивно
 * Используется для отладки
 *
 * @param mixed $var
 * @param boolean $halt
*/
function dump_if_admin($var, $halt = true) {
    if (cmsUser::isAdmin()) {
        dump($var, $halt);
    }
}
