<?php

/**
 * Выводит строку, безопасную для html
 * @param string $string Строка
 * @param boolean $print Печатать результат или возвращать, по умолчанию true
 */
function html($string, $print = true){

    $string = htmlentities($string, ENT_QUOTES | ENT_HTML401, 'UTF-8');

    if($print){
        echo $string; return;
    }

    return $string;

}

/**
 * Очищает строку от тегов и обрезает до нужной длины
 * @param string $string Строка
 * @param integer $max_length Максимальное кол-во символов, по умолчанию false
 * @return string
 */
function html_clean($string, $max_length = false){

    // строка может быть без переносов
    // и после strip_tags не будет пробелов между словами
    $string = str_replace(array("\n", "\r", '<br>', '<br/>'), ' ', $string);
    $string = strip_tags($string);

    if (is_int($max_length)){
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
function html_strip($string, $max_length){
	$length = mb_strlen($string);
	if ($length > $max_length) {
		$string = mb_substr($string, 0, $max_length);
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
function rel_to_href($rel_link, $is_abs = false){

    $lang_href = cmsCore::getLanguageHrefPrefix();

	return ($is_abs ? cmsConfig::get('host') . '/' : cmsConfig::get('root')) .($lang_href ? $lang_href.'/' : '').$rel_link;

}

/**
 * Возвращает ссылку на профиль пользователя
 *
 * @param mixed $user Массив данных пользователя
 * @param mixed $params
 * @return string
 */
function href_to_profile($user, $params = false){

    if(is_array($user)){
        return href_to('users', (empty($user['slug']) ? $user['id'] : $user['slug']), $params);
    } elseif(is_object($user)){
        return href_to('users', (empty($user->slug) ? $user->id : $user->slug), $params);
    }

    return href_to('users', $user, $params);

}

/**
 * Возвращает ссылку на указанное действие контроллера
 * с добавлением пути от корня сайта
 * @param string $controller
 * @param string $action
 * @param array|string|integer $params Параметры, массив
 * @return string
 */
function href_to($controller, $action = '', $params = false){

    $lang_href = cmsCore::getLanguageHrefPrefix();

	return cmsConfig::get('root') .($lang_href ? $lang_href.'/' : ''). href_to_rel($controller, $action, $params);

}

/**
 * Возвращает ссылку на указанное действие контроллера
 * с добавлением хоста сайта
 * @param string $controller
 * @param string $action
 * @param array|string|integer $params Параметры, массив
 * @return string
 */
function href_to_abs($controller, $action = '', $params = false){

    $lang_href = cmsCore::getLanguageHrefPrefix();

	return cmsConfig::get('host') . '/' .($lang_href ? $lang_href.'/' : ''). href_to_rel($controller, $action, $params);

}

/**
 * Возвращает ссылку на указанное действие контроллера без добавления корня URL
 *
 * @param string $controller
 * @param string $action
 * @param array|string|integer $params Параметры, массив
 * @return string
 */
function href_to_rel($controller, $action = '', $params = false){

    $controller = trim($controller, '/ ');

	$ctype_default = cmsConfig::get('ctype_default');

	if ($ctype_default && $action && in_array($controller, $ctype_default) && $action){
		if (preg_match('/([a-z0-9\-\/{}]+)(\.html|\/view\-[a-z0-9\-_]+)$/i', $action)){
			$controller = '';
		}
	}

	$controller_alias = cmsCore::getControllerAliasByName($controller);
	if ($controller_alias) { $controller = $controller_alias; }

    $href = $controller;

	if($action){ $href .= '/' . $action; }
	if($params){
        if (is_array($params)){
            $href .= '/' . implode('/', $params);
        } else {
            $href .= '/' . $params;
        }
    }

    return trim($href, '/');

}

/**
 * Возвращает ссылку на текущую страницу
 * @param boolean $add_host
 * @return string
 */
function href_to_current($add_host = false){
    $lang_href = cmsCore::getLanguageHrefPrefix();
    $lang_href = ($lang_href ? '/'.$lang_href : '');
    if($add_host){
        return cmsConfig::get('host').$lang_href.$_SERVER['REQUEST_URI'];
    } else {
        return $lang_href.$_SERVER['REQUEST_URI'];
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
 * @param array $attributes
 * @return string
 */
function html_attr_str($attributes){
    $attr_str = '';
    unset($attributes['class']);
    if (is_array($attributes)){
        foreach($attributes as $key=>$val){
            if(is_bool($val)){
                if($val === true){
                    $attr_str .= "{$key} ";
                }
                continue;
            }
            $attr_str .= "{$key}=\"{$val}\" ";
        }
    }
    return $attr_str;
}

function default_images($type, $preset) {
    return array(
        $preset => 'default/'.$type.'_'.$preset.'.png'
    );
}

/**
 * Возвращает ссылку на аватар пользователя
 * @param array|yaml $avatars Все изображения аватара
 * @param string $size_preset Название пресета
 * @return string
 */
function html_avatar_image_src($avatars, $size_preset = 'small', $is_relative = true){

    $config = cmsConfig::getInstance();

    if (empty($avatars)){
		$avatars = array(
            'normal' => 'default/avatar.jpg',
            'small' => 'default/avatar_small.jpg',
            'micro' => 'default/avatar_micro.png'
        );
    }

    if (!is_array($avatars)){
        $avatars = cmsModel::yamlToArray($avatars);
    }

    $src = $avatars[ $size_preset ];

	if (strpos($src, $config->upload_host) === false){
        if($is_relative){
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
function html_image_src($image, $size_preset='small', $is_add_host=false, $is_relative=true){

    $config = cmsConfig::getInstance();

    if (!is_array($image)){
        $image = cmsModel::yamlToArray($image);
    }

    if (!$image){
        return false;
    }

    $keys = array_keys($image);
    if ($keys[0] === 0) { $image = $image[0]; }

	if (isset($image[ $size_preset ])){
		$src = $image[ $size_preset ];
	} else {
		return false;
	}

    if ($is_add_host && strpos($src, $config->upload_host) === false){
        if($is_relative){
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
 * @param string $field_id id элемента
 * @param string $content Текст редактора
 * @param string $wysiwyg Имя редактора
 * @param array $config Параметры редактора
 * @return string HTML код
 */
function html_wysiwyg($field_id, $content = '', $wysiwyg = false, $config = array()){

    if (!$wysiwyg){

        if($wysiwyg === null){
            return '<textarea class="textarea" id="'.$field_id.'" name="'.$field_id.'">'.html($content, false).'</textarea>';
        }

        $wysiwyg = cmsConfig::get('default_editor');

    }

	$connector = 'wysiwyg/' . $wysiwyg . '/wysiwyg.class.php';

	if (!cmsCore::includeFile($connector)){
		return '<textarea class="error_wysiwyg" id="'.$field_id.'" name="'.$field_id.'">'.html($content, false).'</textarea>';
	}

    cmsCore::loadControllerLanguage($wysiwyg);

    list($field_id, $content, $wysiwyg, $config) = cmsEventsManager::hook(['display_wysiwyg_editor', 'display_'.$wysiwyg.'_wysiwyg_editor'], array($field_id, $content, $wysiwyg, $config));

    $class_name = 'cmsWysiwyg' . ucfirst($wysiwyg);

    $editor = new $class_name($config);

    // $config передаём для совместимости
    ob_start(); $editor->displayEditor($field_id, $content, $config);

    return ob_get_clean();

}

/**
 * Редактор markitup
 * функция совместимости
 *
 * @param string $field_id
 * @param string $content
 * @param array $options
 * @return string
 */
function html_editor($field_id, $content = '', $options = array()) {
    return html_wysiwyg($field_id, $content, 'markitup', $options);
}

function html_select_range($name, $start, $end, $step, $add_lead_zero=false, $selected='', $attributes=array()){

    $items = array();

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
 * @param int $number
 * @return string
 */
function html_signed_num($number){
    if ($number > 0){
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
function html_signed_class($number){
    if ($number > 0){
        return "positive";
    } else if ($number < 0){
        return "negative";
    } else {
        return "zero";
    }
}

/**
 * Возвращает скрытое поле, содержащее актуальный CSRF-токен
 * @return string
 */
function html_csrf_token(){
    return html_input('hidden', 'csrf_token', cmsForm::getCSRFToken());
}

/**
 * Возвращает число с числительным в нужном склонении
 * @param int $num
 * @param string $one
 * @param string $two
 * @param string $many
 * @param string $zero_text
 * @return string
 */
function html_spellcount($num, $one, $two = false, $many = false, $zero_text = LANG_NO) {

    if (!$two && !$many){
        list($one, $two, $many) = explode('|', $one);
    }

	if (!$num){
		return $zero_text.' '.$many;
	}

    return $num.' '.html_spellcount_only($num, $one, $two, $many);

}

function html_spellcount_only($num, $one, $two = false, $many = false) {

    if (!$two && !$many){
        list($one, $two, $many) = explode('|', $one);
    }

	if (strpos($num, '.') !== false){
		return $two;
	}

    if ($num%10 == 1 && $num%100 != 11){
        return $one;
    }
    elseif($num%10 >= 2 && $num%10 <= 4 && ($num%100 < 10 || $num%100 >= 20)){
        return $two;
    }
    else{
        return $many;
    }

    return $one;

}

/**
 * Возвращает отформатированный размер файла с единицей измерения
 * @param int $bytes
 * @param bool $round
 * @return string
 */
function html_file_size($bytes, $round=false){

    if(empty($bytes)) { return 0; }

    $s = array(LANG_B, LANG_KB, LANG_MB, LANG_GB, LANG_TB, LANG_PB);
    $e = floor(log($bytes)/log(1024));

    $pattern = $round ? '%d' : '%.2f';

    $output = sprintf($pattern.' '.$s[$e], ($bytes/pow(1024, floor($e))));

    return $output;

}

/**
 * Возвращает склеенный в одну строку массив строк
 * @param array $array
 * @return string
 */
function html_each($array){

    $result = '';

    if (is_array($array)){
        $result = implode('', $array);
    }

    return $result;

}

/**
 * Вырезает из HTML-кода пробелы, табуляции и переносы строк
 * @param string $html
 * @return string
 */
function html_minify($html){

    $search = array(
        '/\>[^\S ]+/s',
        '/[^\S ]+\</s',
        '/(\s)+/s'
    );

    $replace = array(
        '>',
        '<',
        '\\1'
    );

    $html = preg_replace($search, $replace, $html);

    return $html;

}

function nf($number, $decimals=2, $thousands_sep=''){
    if (!$number) { return 0; }
    return number_format((double)str_replace(',', '.', $number), $decimals, '.', $thousands_sep);
}
