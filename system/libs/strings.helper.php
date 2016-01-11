<?php

/**
 * Разбивает строку по разделителю, затем собирает обратно в CamelCase
 * Например "my_own_string" => "MyOwnString", разделитель "_"
 *
 * @param char $delimiter Разделитель
 * @param string $string Исходная строка
 * @return string
 */
function string_to_camel($delimiter, $string){

    $result = '';
    $words = explode($delimiter, mb_strtolower($string));

    foreach($words as $word){
        $result .= ucfirst($word);
    }

    return $result;

}

/**
 * Вырезает теги <br> из строки
 * @param string $string
 * @return string
 */
function string_strip_br($string){

    return str_replace('<br>', '', str_replace('<br/>', '', $string));

}

/**
 * Возвращает значение языковой константы
 * Если константа не найдена, возвращает ее имя или значение по-умолчанию
 *
 * Префикс LANG_ в имени константы указывать не нужно
 * Регистр не имеет значения
 *
 * @param string $constant
 * @param string $default
 * @return string
 */
function string_lang($constant, $default=false){

    $constant = mb_strtoupper($constant);

    if (!$default) { $default = $constant; }

    $constant = mb_strtoupper($constant);

    if (!mb_strpos($constant, 'LANG_')===0){
        $constant = 'LANG_' . $constant;
    }

    if (defined($constant)){
        $string = constant('LANG_'.$constant);
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
function string_mask_to_regular($mask){
    $regular = trim($mask);
    $regular = str_replace('/', '\/', $regular);
    $regular = str_replace('*', '(.*)', $regular);
    $regular = str_replace('%', '([0-9]+)', $regular);
    return $regular;
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
function string_parse_list($string_list){

    if (!$string_list) { return array(); }

    $user = cmsUser::getInstance();

    $rows = explode("\n", $string_list);

    $list = array();

    foreach($rows as $row){

        if (!$row) { continue; }

        $row = trim($row);

        if ( preg_match('/^{(.*)}$/i', $row, $matches) ){
            if (!$user->is_logged){ continue; }
            $row = trim($matches[1]);
        }

        if (!mb_strstr($row, '|')){
            $list[] = array('value' => trim($row));
        } else {
            list($id, $value) = explode("|", $row);
            $list[] = array(
                'id' => trim($id),
                'value' => trim($value)
            );
        }

    }

    return $list;

}

function string_explode_list($string_list){

    $items = array();
    $rows = explode("\n", trim($string_list));
    if (is_array($rows)){
        foreach($rows as $count=>$row){
            if (mb_strpos($row, '|')){
                list($index, $value) = explode('|', trim($row));
            } else {
                $index = $count + 1;
                $value = $row;
            }
            $items[trim($index)] = trim($value);
        }
    }
    return $items;

}

/**
 * Получает список аналогично string_parse_list() и ищет вхождение в него
 * заданной строки
 *
 * @param string $string
 * @param string $mask_list
 * @return boolean
 */
function string_in_mask_list($string, $mask_list){

    if (!$mask_list) { return false; }

    $mask_list = explode("\n", $mask_list);

    foreach($mask_list as $item){

        $regular = string_mask_to_regular($item);
        $regular = "/^{$regular}$/iu";

        if (preg_match($regular, $string)){
            return true;
        }

    }

    return false;

}

/**
 * Генерирует случайную последовательность символов заданной длины
 * @param int $length
 * @return string
 */
function string_random($length=32, $seed=''){

    $string = md5(md5(session_id() . '$' . microtime(true) . '$' . uniqid()) . '$' . $seed);

    if ($length < 32) { $string = mb_substr($string, 0, $length); }

    return $string;

}

/**
 * Выводит разницу между переданной датой и текущим временем
 * в виде читабельной строки со склонениями
 *
 * Пример вывода: "2 года 16 дней 5 часов 12 минут"
 *
 * @param string $date
 * @param array $options Массив элементов для перечисления: y, m, d, h, i
 * @param bool $is_add_back Добавлять к строке слово "назад"?
 * @return string
 */
function string_date_age($date, $options, $is_add_back=false){

    if (!$date) { return; }

    $diff = real_date_diff($date);

    $diff_str = array();

    if (in_array('y', $options) && $diff[0]){
        $diff_str[] = html_spellcount($diff[0], LANG_YEAR1, LANG_YEAR2, LANG_YEAR10);
    }
    if (in_array('m', $options) && $diff[1]){
        $diff_str[] = html_spellcount($diff[1], LANG_MONTH1, LANG_MONTH2, LANG_MONTH10);
    }
    if (in_array('d', $options) && $diff[2]){
        $diff_str[] = html_spellcount($diff[2], LANG_DAY1, LANG_DAY2, LANG_DAY10);
    }
    if (in_array('h', $options) && $diff[3]){
        $diff_str[] = html_spellcount($diff[3], LANG_HOUR1, LANG_HOUR2, LANG_HOUR10);
    }
    if (in_array('i', $options) && $diff[4]){
        $diff_str[] = html_spellcount($diff[4], LANG_MINUTE1, LANG_MINUTE2, LANG_MINUTE10);
    }

    if (!$diff_str) {
        return LANG_SECONDS_AGO;
    } else {
        $diff_str = trim( implode(' ', $diff_str) );
        return $is_add_back ? sprintf(LANG_DATE_AGO, $diff_str) : $diff_str;
    }

}

/**
 * Выводит максимальную разницу между переданной датой и текущим временем
 * в виде читабельной строки со склонением
 *
 * Пример вывода: "3 дня"
 *
 * @param string $date
 * @param bool $is_add_back Добавлять к строке слово "назад"?
 * @return string
 */
function string_date_age_max($date, $is_add_back=false){

    if (!$date) { return; }

    $diff = real_date_diff($date);

    $diff_str = '';

    if ($diff[0]){
        $diff_str = html_spellcount($diff[0], LANG_YEAR1, LANG_YEAR2, LANG_YEAR10);
    } else
    if ($diff[1]){
        $diff_str = html_spellcount($diff[1], LANG_MONTH1, LANG_MONTH2, LANG_MONTH10);
    } else
    if ($diff[2]){
        $diff_str = html_spellcount($diff[2], LANG_DAY1, LANG_DAY2, LANG_DAY10);
    } else
    if ($diff[3]){
        $diff_str = html_spellcount($diff[3], LANG_HOUR1, LANG_HOUR2, LANG_HOUR10);
    } else
    if ($diff[4]){
        $diff_str = html_spellcount($diff[4], LANG_MINUTE1, LANG_MINUTE2, LANG_MINUTE10);
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
function real_date_diff($date1, $date2 = NULL){

    $diff = array();

    if (!is_string($date1)){ return false; }

    //Если вторая дата не задана принимаем ее как текущую
    if(!$date2) {
        $cd = getdate();
        $date2 = $cd['year'].'-'.$cd['mon'].'-'.$cd['mday'].' '.$cd['hours'].':'.$cd['minutes'].':'.$cd['seconds'];
    }

    //Преобразуем даты в массив
    $pattern = '/(\d+)-(\d+)-(\d+)(\s+(\d+):(\d+):(\d+))?/';
    preg_match($pattern, $date1, $matches);
    $d1 = array((int)$matches[1], (int)$matches[2], (int)$matches[3], (int)$matches[5], (int)$matches[6], (int)$matches[7]);
    preg_match($pattern, $date2, $matches);
    $d2 = array((int)$matches[1], (int)$matches[2], (int)$matches[3], (int)$matches[5], (int)$matches[6], (int)$matches[7]);

    //Если вторая дата меньше чем первая, меняем их местами
    for($i=0; $i<count($d2); $i++) {
        if($d2[$i]>$d1[$i]) break;
        if($d2[$i]<$d1[$i]) {
            $t = $d1;
            $d1 = $d2;
            $d2 = $t;
            break;
        }
    }

    //Вычисляем разность между датами (как в столбик)
    $md1 = array(31, $d1[0]%4||(!($d1[0]%100)&&$d1[0]%400)?28:29, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31);
    $md2 = array(31, $d2[0]%4||(!($d2[0]%100)&&$d2[0]%400)?28:29, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31);
    $min_v = array(NULL, 1, 1, 0, 0, 0);
    $max_v = array(NULL, 12, $d2[1]==1?$md2[11]:$md2[$d2[1]-2], 23, 59, 59);
    for($i=5; $i>=0; $i--) {
        if($d2[$i]<$min_v[$i]) {
            $d2[$i-1]--;
            $d2[$i]=$max_v[$i];
        }
        $diff[$i] = $d2[$i]-$d1[$i];
        if($diff[$i]<0) {
            $d2[$i-1]--;
            $i==2 ? $diff[$i] += $md1[$d1[1]-1] : $diff[$i] += $max_v[$i]-$min_v[$i]+1;
        }
    }

    //Возвращаем результат
    return $diff;

}

/**
 * Находит в строке все выжения вида {user.property} и заменяет property
 * на соответствующее свойство объекта cmsUser
 *
 * @param string $string
 * @return string
 */
function string_replace_user_properties($string){

    $matches_count = preg_match_all('/{user.([a-z0-9_]+)}/i', $string, $matches);

    if ($matches_count){

        $user = cmsUser::getInstance();

        for($i=0; $i<$matches_count; $i++){

            $tag = $matches[0][$i];
            $property = $matches[1][$i];

            if (isset($user->$property)){
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
function string_replace_keys_values($string, $data){

    if(strpos($string, '{') === false){ return $string; }

	foreach($data as $k=>$v){
		if (is_array($v) || is_object($v)) { unset($data[$k]); }
	}

    $keys = array_map(function($key){ return '{'.$key.'}'; }, array_keys($data));

    return str_replace($keys, array_values($data), $string);

}

/**
 * Делает активными гиперссылки внутри строки
 *
 * @param string $string
 * @return string
 */
function string_make_links($string){
    return preg_replace('@(https?://([-\w\.]+[-\w])+(:\d+)?(/([\w/_\.#-]*(\?\S+)?[^\.\s])?)?)@', '<a href="$1" target="_blank">$1</a>', $string);
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
function string_get_meta_keywords($text, $min_length=5, $limit=10){

    $stat = array();

    $text = str_replace(array("\n", '<br>', '<br/>'), ' ', $text);
    $text = strip_tags($text);
    $text = mb_strtolower($text);

    $words = explode(' ', $text);

    foreach($words as $word){

        $word = trim($word);
        $word = str_replace(array('(',')','+','-','.','!',':','{','}','|','"',',',"'"), '', $word);
        $word = preg_replace("/\.,\(\)\{\}/i", '', $word);

        if (mb_strlen($word)>=$min_length){
            $stat[$word] = isset($stat[$word]) ? $stat[$word]+1 : 1;
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
function string_get_meta_description($text, $limit=250){

    return string_short($text, $limit);

}

/**
 * Обрезает исходный текст до указанной длины (или последнего предложения),
 * удаляя HTML-разметку
 *
 * @param string $text
 * @param int $limit Максимальная длина результата
 * @return string
 */
function string_short($text, $limit=0){

    // строка может быть без переносов
    // и после strip_tags не будет пробелов между словами
    $text = str_replace(array("\n", "\r", '<br>', '<br/>'), ' ', $text);
    $text = strip_tags($text);

    if (!$limit || mb_strlen($text) <= $limit) { return $text; }

    $text = mb_substr($text, 0, $limit);
    $text = preg_replace('/ |\s{3,}/',' ',$text);

    preg_match('/^(.*)([.!?])(.*)$/i', $text, $matches);

    if (!$matches){
        return $text;
    } else {
        return $matches[1];
    }

    return $text;

}

/**
 * Вырезает из строки CSS/JS-комментарии, табуляции, переносы строк и лишние пробелы
  *
 * @param string $string
  * @return string
 */
function string_compress($string){

    $string = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $string);
    $string = str_replace(array("\r\n", "\r", "\n", "\t", '  ', '    ', '    '), '', $string);

    return $string;

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
function array_collection_to_list($collection, $key, $value){

    $list = array();

    if (is_array($collection)){
        foreach($collection as $item){
            $list[ $item[$key] ] = $item[$value];
        }
    }

    return $list;

}

//============================================================================//

/**
 * Выводит переменную рекурсивно
 * @param mixed $var
*/
function dump($var, $halt=true){
    echo '<pre>'; print_r($var); echo '</pre>';
    if ($halt) { die(); }
}
