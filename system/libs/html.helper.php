<?php

/**
 * Выводит строку, безопасную для html
 * @param string $string
 */
function html($string){
    echo htmlspecialchars($string);
}

/**
 * Выводит тег <a>
 * @param string $title Название
 * @param string $href Ссылка
 */
function html_link($title, $href){
	echo '<a href="'.$href.'">'.htmlspecialchars($title).'</a>';
}

/**
 * Очищает строку от тегов и обрезает до нужной длины
 * @param string $string Строка
 * @param int $max_length Максимальное кол-во символов, по умолчанию false
 * @return string
 */
function html_clean($string, $max_length=false){

    $string = strip_tags($string);

    if (is_int($max_length)){
        $string = html_strip($string, $max_length);
    }

    return $string;

}

/**
 * Обрезает строку до заданного кол-ва символов
 * @param string $string Строка
 * @param int $max_length Кол-во символов, которые нужно оставить от начала строки
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
 * Возвращает панель со страницами
 *
 * @param int $page Текущая страница
 * @param int $perpage Записей на одной странице
 * @param int $total Количество записей
 * @param str $base_uri Базовый URL, может быть массивом из элементов first и base
 * @param str $query Массив параметров запроса
 */
function html_pagebar($page, $perpage, $total, $base_uri=false, $query=array()){

	if (!$total){ return; }

    $pages = ceil($total / $perpage);
    if($pages<=1) { return; }

    $core = cmsCore::getInstance();

    $anchor = '';

    if (is_string($base_uri) && mb_strstr($base_uri, '#')){
        list($base_uri, $anchor) = explode('#', $base_uri);
    }

    if ($anchor) { $anchor = '#' . $anchor; }

    if (!$base_uri) { $base_uri = $core->uri_absolute; }

    if (!is_array($base_uri)){
        $base_uri = array(
            'first'=>$base_uri,
            'base'=>$base_uri
        );
    }

    if (!is_array($query)){
        parse_str($query, $query);
    }

    $html   = '';

    $html .= '<div class="pagebar">';

	if (($page > 1) || ($page < $pages)) {

		$html .= '<span class="pagebar_nav">';

		if ($page > 1){
			$query['page'] = ($page-1);
			$uri = ($query['page']==1 ? $base_uri['first'] : $base_uri['base']);
			$sep = mb_strstr($uri, '?') ? '&' : '?';
			if ($query['page'] == 1) { unset($query['page']); }
			$html .= ' <a href="'. $uri . ($query ? $sep .http_build_query($query) : '') . $anchor . '" class="pagebar_page">&larr; '.LANG_PAGE_PREV.'</a> ';
		} else {
			$html .= ' <span class="pagebar_page disabled">&larr; '.LANG_PAGE_PREV.'</span> ';
		}

		if ($page < $pages){
			$query['page'] = ($page+1);
			$uri = ($query['page']==1 ? $base_uri['first'] : $base_uri['base']);
			$sep = mb_strstr($uri, '?') ? '&' : '?';
			if ($query['page'] == 1) { unset($query['page']); }
			$html .= ' <a href="'. $uri . ($query ? $sep.http_build_query($query) : '') . $anchor . '" class="pagebar_page">'.LANG_PAGE_NEXT.' &rarr;</a> ';
		} else {
			$html .= ' <span class="pagebar_page disabled">'.LANG_PAGE_NEXT.' &rarr;</span> ';
		}

		$html .= '</span>';

	}

	$span = 3;
	if ($page - $span < 1) { $p_start = 1; } else { $p_start = $page - $span; }
	if ($page + $span > $pages) { $p_end = $pages; } else { $p_end = $page + $span; }

	$html .= '<span class="pagebar_pages">';

	if ($page > $span+1){
        $query['page'] = 1;
        $uri = ($query['page']==1 ? $base_uri['first'] : $base_uri['base']);
        $sep = mb_strstr($uri, '?') ? '&' : '?';
        if ($query['page'] == 1) { unset($query['page']); }
        $html .= ' <a href="'. $uri . ($query ? $sep.http_build_query($query) : '') . $anchor . '" class="pagebar_page">'.LANG_PAGE_FIRST.'</a> ';
	}

    for ($p=$p_start; $p<=$p_end; $p++){
        if ($p != $page) {
            $query['page'] = $p;
            $uri = ($query['page']==1 ? $base_uri['first'] : $base_uri['base']);
            $sep = mb_strstr($uri, '?') ? '&' : '?';
            if ($query['page'] == 1) { unset($query['page']); }
            $html .= ' <a href="'. $uri . ($query ? $sep.http_build_query($query) : '') . $anchor . '" class="pagebar_page">'.$p.'</a> ';
        } else {
            $html .= '<span class="pagebar_current">'.$p.'</span>';
        }
    }

	if ($page < $pages - $span){
        $query['page'] = $pages;
        $uri = ($query['page']==1 ? $base_uri['first'] : $base_uri['base']);
        $sep = mb_strstr($uri, '?') ? '&' : '?';
        if ($query['page'] == 1) { unset($query['page']); }
        $html .= ' <a href="'. $uri . ($query ? $sep.http_build_query($query) : '') . $anchor . '" class="pagebar_page">'.LANG_PAGE_LAST.'</a> ';
	}

	$html .= '</span>';

    $from   = $page * $perpage - $perpage + 1;
    $to     = $page * $perpage; if ($to>$total) { $to = $total; }

    $html  .= '<div class="pagebar_notice">'.sprintf(LANG_PAGES_SHOWN, $from, $to, $total).'</div>';

    $html .= '</div>';

	return $html;

}

/**
 * Возвращает ссылку на указанное действие контроллера
 * с добавлением пути от корня сайта
 * @param string $controller
 * @param string $action
 * @param array|str|int $params Параметры, массив
 * @return string
 */
function href_to($controller, $action='', $params=false){

	return cmsConfig::get('root') . href_to_rel($controller, $action, $params);

}

/**
 * Возвращает ссылку на указанное действие контроллера
 * с добавлением хоста сайта
 * @param string $controller
 * @param string $action
 * @param array|str|int $params Параметры, массив
 * @return string
 */
function href_to_abs($controller, $action='', $params=false){

	return cmsConfig::get('host') . '/' . href_to_rel($controller, $action, $params);

}

/**
 * Возвращает ссылку на указанное действие контроллера без добавления корня URL
 *
 * @param string $controller
 * @param string $action
 * @param array|str|int $params Параметры, массив
 * @return string
 */
function href_to_rel($controller, $action='', $params=false){

    $controller = trim($controller, '/ ');

	$ctype_default = cmsConfig::get('ctype_default');

	if ($ctype_default && $ctype_default == $controller){
		if (preg_match('/([a-zA-Z0-9\-\/]+).html$/i', $action)){
			$controller = '';
		}
	}

	$controller_alias = cmsCore::getControllerAliasByName($controller);
	if ($controller_alias) { $controller = $controller_alias; }

	$href = $controller;

	if($action){ $href .= '/' . $action; }
	if($params){
        if (is_array($params)){
            $href .= '/' . implode("/", $params);
        } else {
            $href .= '/' . $params;
        }
    }

    return trim($href, '/');

}

/**
 * Возвращает ссылку на текущую страницу
 * @return string
 */
function href_to_current(){
    return $_SERVER['REQUEST_URI'];
}

/**
 * Возвращает ссылку на главную страницу сайта
 * @return string
 */
function href_to_home(){
    return cmsConfig::get('host');
}

/**
 * Возвращает отформатированную строку аттрибутов тега
 * @param array $attributes
 * @return string
 */
function html_attr_str($attributes){
    $attr_str = '';
    unset($attributes['class']);
    if (sizeof($attributes)){
        foreach($attributes as $key=>$val){
            $attr_str .= "{$key}=\"{$val}\" ";
        }
    }
    return $attr_str;
}

/**
 * Возвращает тег <input>
 *
 * @param string $type Тип поля
 * @param string $name Имя поля
 * @param string $value Значение по-умолчанию
 * @return html
 */
function html_input($type='text', $name='', $value='', $attributes=array()){
    if ($type=='password'){ $attributes['autocomplete'] = 'off'; }
    $attr_str = html_attr_str($attributes);
    $class = 'input';
    if (isset($attributes['class'])) { $class .= ' '.$attributes['class']; }
	return '<input type="'.$type.'" class="'.$class.'" name="'.$name.'" value="'.htmlspecialchars($value).'" '.$attr_str.'/>';
}

function html_file_input($name, $attributes=array()){
    $attr_str = html_attr_str($attributes);
    $class = 'file-input';
    if (isset($attributes['class'])) { $class .= ' '.$attributes['class']; }
	return '<input type="file" class="'.$class.'" name="'.$name.'" '.$attr_str.'/>';
}

function html_textarea($name='', $value='', $attributes=array()){
    $attr_str = html_attr_str($attributes);
    $class = 'textarea';
    if (isset($attributes['class'])) { $class .= ' '.$attributes['class']; }
	$html = '<textarea name="'.$name.'" class="'.$class.'" '.$attr_str.'>'.htmlspecialchars($value).'</textarea>';
	return $html;
}

function html_back_button(){
	return '<div class="back_button"><a href="javascript:window.history.go(-1);">'.LANG_BACK_BUTTON.'</a></div>';
}

function html_checkbox($name, $checked=false, $value=1, $attributes=array()){
    if ($checked) { $attributes['checked'] = 'checked'; }
    $attr_str = html_attr_str($attributes);
    $class = 'input-checkbox';
    if (isset($attributes['class'])) { $class .= ' '.$attributes['class']; }
	return '<input type="checkbox" class="'.$class.'" name="'.$name.'" value="'.$value.'" '.$attr_str.'/>';
}

function html_radio($name, $checked=false, $value=1, $attributes=array()){
    if ($checked) { $attributes['checked'] = 'checked'; }
    $attr_str = html_attr_str($attributes);
	return '<input type="radio" class="input_radio" name="'.$name.'" value="'.$value.'" '.$attr_str.'/>';
}

function html_date($date=false, $is_time=false){
    $config = cmsConfig::getInstance();
    $timestamp = $date ? strtotime($date) : time();
    $date = htmlspecialchars(date($config->date_format, $timestamp));
    if ($is_time){ $date .= ' <span class="time">' . date('H:i', $timestamp). '</span>'; }
    return $date;
}

function html_time($date=false){
    $timestamp = $date ? strtotime($date) : time();
    return date('H:i', $timestamp);
}

function html_date_time($date=false){
    return html_date($date, true);
}

function html_datepicker($name='', $value='', $attributes=array()){
    $config = cmsConfig::getInstance();
    if (isset($attributes['id'])){
        $id = $attributes['id'];
        unset($attributes['id']);
    } else {
        $id = $name;
    }
    $attr_str = html_attr_str($attributes);
	$html  = '<input type="text" name="'.$name.'" value="'.htmlspecialchars($value).'" class="date-input"  id="'.$id.'" '.$attr_str.'/>';
    $html .= '<script type="text/javascript">';
    $html .= "$(document).ready(function(){ $('#{$id}').datepicker({showStatus: true, showOn: 'both', dateFormat:'{$config->date_format_js}'}); });";
    $html .= '</script>';
    return $html;
}

/**
 * Возвращает кнопку "Отправить" <input type="submit">
 *
 * @param string $caption
 * @return html
 */
function html_submit($caption=LANG_SUBMIT, $name='submit', $attributes=array()){
    $attr_str = html_attr_str($attributes);
    $class = 'button-submit';
    if (isset($attributes['class'])) { $class .= ' '.$attributes['class']; }
	return '<input class="'.$class.'" type="submit" name="'.$name.'" value="'.htmlspecialchars($caption).'" '.$attr_str.'/>';
}

/**
 * Возвращает html-код кнопки
 *
 * @param str $caption Заголовок
 * @param str $name Название кнопки
 * @param str $onclick Содержимое аттрибута onclick (javascript)
 * @return html
 */
function html_button($caption, $name, $onclick='', $attributes=array()){
    $attr_str = html_attr_str($attributes);
    $class = 'button';
    if (isset($attributes['class'])) { $class .= ' '.$attributes['class']; }
	return '<input type="button" class="'.$class.'" name="'.$name.'" value="'.htmlspecialchars($caption).'" onclick="'.$onclick.'" '.$attr_str.'/>';
}

/**
 * Возвращает ссылку на аватар пользователя
 * @param array|yaml $avatars Все изображения аватара
 * @param string $size_preset Название пресета
 * @return string
 */
function html_avatar_image_src($avatars, $size_preset='small'){

    $config = cmsConfig::getInstance();

    $default = array(
        'normal' => 'default/avatar.jpg',
        'small' => 'default/avatar_small.jpg',
        'micro' => 'default/avatar_micro.png'
    );

    if (empty($avatars)){
		$avatars = $default;
    }

    if (!is_array($avatars)){
        $avatars = cmsModel::yamlToArray($avatars);
    }

    $src = $avatars[ $size_preset ];

	if (strpos($src, $config->upload_host) === false){
        $src = $config->upload_host . '/' . $src;
    }

    return $src;

}

/**
 * Возвращает тег <img> аватара пользователя
 * @param array|yaml $avatars Все изображения аватара
 * @param string $size_preset Название пресета
 * @param string $alt Замещающий текст изображения
 * @return string
 */
function html_avatar_image($avatars, $size_preset='small', $alt=''){

    $src = html_avatar_image_src($avatars, $size_preset);

	$size = $size_preset == 'micro' ? 'width="32" height="32"' : '';

    return '<img src="'.$src.'" '.$size.' alt="'.htmlspecialchars($alt).'" />';

}

/**
 * Возвращает тег <img>
 * @param array|yaml $image Все размеры заданного изображения
 * @param string $size_preset Название пресета
 * @param string $alt Замещающий текст изображения
 * @return string
 */
function html_image($image, $size_preset='small', $alt=''){

	$size = $size_preset == 'micro' ? 'width="32" height="32"' : '';

	$src = html_image_src($image, $size_preset, true);

	if (!$src) { return false; }

    return '<img src="'.$src.'" '.$size.' alt="'.htmlspecialchars($alt).'" />';

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
    if ($keys[0]===0) { $image = $image[0]; }

	if (isset($image[ $size_preset ])){
		$src = $image[ $size_preset ];
	} else {
		return false;
	}

    if ($is_add_host && !strstr($src, $config->upload_host)){
        if($is_relative){
            $src = $config->upload_host . '/' . $src;
        } else {
            $src = $config->upload_host_abs . '/' . $src;
        }
    }

    return $src;

}

function html_wysiwyg($field_id, $content='', $wysiwyg=false){

    $config = cmsConfig::getInstance();

    if (!$wysiwyg){
        $config = cmsConfig::getInstance();
        $wysiwyg = $config->wysiwyg;
    }

	$connector = 'wysiwyg/' . $wysiwyg . '/wysiwyg.class.php';

	if (!file_exists($config->root_path . $connector)){
		return '<textarea id="'.$field_id.'" name="'.$field_id.'">'.$content.'</textarea>';
	}

    cmsCore::includeFile($connector);

    $class_name = 'cmsWysiwyg' . ucfirst($wysiwyg);

    $editor = new $class_name();

    ob_start(); $editor->displayEditor($field_id, $content);

    return ob_get_clean();

}

function html_editor($field_id, $content='', $options=array()){

    $markitup_controller = cmsCore::getController('markitup', new cmsRequest(array(), cmsRequest::CTX_INTERNAL));

    return $markitup_controller->getEditorWidget($field_id, $content, $options);

}

/**
 * Генерирует список опций
 *
 * @param string $name Имя списка
 * @param array $items Массив элементов списка (значение => заголовок)
 * @param string $selected Значение выбранного элемента
 * @param array $attributes Массив аттрибутов тега
 * @return html
 */
function html_select($name, $items, $selected = '', $attributes = array()){

    $attr_str = html_attr_str($attributes);
    $class = isset($attributes['class']) ? ' class="'.$attributes['class'].'"' : '';
    $html = '<select name="'.$name.'" '.$attr_str.$class.'>'."\n";

    $optgroup = false;

    if($items && is_array($items)){
        foreach($items as $value => $title){

            if(is_array($title)){
                if($optgroup !== false){
                    $html .= "\t".'</optgroup>'."\n";
                    $optgroup = false;
                }
                $optgroup = true;
                $html .= "\t".'<optgroup label="'.$title[0].'">'."\n";
                continue;
            }

            $sel = ((string) $selected === (string) $value) ? 'selected' : '';
            $html .= "\t".'<option value="'.htmlspecialchars($value).'" '.$sel.'>'.htmlspecialchars($title).'</option>'."\n";
        }
    }

    if($optgroup !== false){
        $html .= "\t".'</optgroup>'."\n";
    }
    
    $html .= '</select>'."\n";
    return $html;
    
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
 * Генерирует список опций с множественным выбором
 *
 * @param string $name Имя списка
 * @param array $items Массив элементов списка (значение => заголовок)
 * @param string $selected Массив значений выбранных элементов
 * @param array $attributes Массив аттрибутов тега
 * @return html
 */
function html_select_multiple($name, $items, $selected=array(), $attributes=array(), $is_tree=false){
    $attr_str = html_attr_str($attributes);
	$html = '<div class="input_checkbox_list" '.$attr_str.'>'."\n";
    $start_level = false;
    foreach ($items as $value=>$title){

        $checked = is_array($selected) && in_array($value, $selected);

        if ($is_tree){

            $level = mb_strlen(str_replace(' ', '', $title)) - mb_strlen(ltrim(str_replace(' ', '', $title), '-'));

            if ($start_level === false) { $start_level = $level; }

            $level = $level - $start_level;

            $title = ltrim($title, '- ');

            $html .= "\t" . '<label '. ($level>0 ? 'style="margin-left:'.($level*20).'px"' : ''). '>' .
                    html_checkbox($name.'[]', $checked, $value) . ' ' .
                    htmlspecialchars($title) . '</label><br>' . "\n";

        } else {

            $html .= "\t" . '<label>' .
                    html_checkbox($name.'[]', $checked, $value) . ' ' .
                    htmlspecialchars($title) . '</label>' . "\n";

        }

	}
	$html .= '</div>'."\n";
	return $html;
}

/**
 * Генерирует и возвращает дерево категорий в виде комбо-бокса
 * @param array $tree Массив с элементами дерева NS
 * @param int $selected_id ID выбранного элемента
 * @return html
 */
function html_category_list($tree, $selected_id=0){
	$html = '<select name="category_id" id="category_id" class="combobox">'."\n";
	foreach ($tree as $cat){
		$padding = str_repeat('---', $cat['ns_level']).' ';
		if ($selected_id == $cat['id']) { $selected = 'selected'; } else { $selected = ''; }
		$html .= "\t" . '<option value="'.$cat['id'].'" '.$selected.'>'.$padding.' '.htmlspecialchars($cat['title']).'</option>' . "\n";
	}
    $html .= '</select>'."\n";
	return $html;
}

/**
 * Генерирует две радио-кнопки ВКЛ и ВЫКЛ
 *
 * @param string $name
 * @param bool $active
 * @return html
 */
function html_switch($name, $active){
	$html = '';
	$html .= '<label><input type="radio" name="'.$name.'" value="1" '. ($active ? 'checked' : '') .'/> ' . LANG_ON . "</label> \n";
	$html .= '<label><input type="radio" name="'.$name.'" value="0" '. (!$active ? 'checked' : '') .'/> ' . LANG_OFF . "</label> \n";
	return $html;
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

function html_bool_span($value, $condition){
    if ($condition){
        return '<span class="positive">' . $value . '</span>';
    } else {
        return '<span class="negative">' . $value . '</span>';
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
 * @return string
 */
function html_spellcount($num, $one, $two=false, $many=false) {

    if (!$two && !$many){
        list($one, $two, $many) = explode('|', $one);
    }

	if (mb_strstr($num, '.')){
		return $num.' '.$two;
	}

	if ($num==0){
		return LANG_NO . ' ' . $many;
	}

    if ($num%10==1 && $num%100!=11){
        return $num.' '.$one;
    }
    elseif($num%10>=2 && $num%10<=4 && ($num%100<10 || $num%100>=20)){
        return $num.' '.$two;
    }
    else{
        return $num.' '.$many;
    }

    return $num.' '.$one;

}

function html_spellcount_only($num, $one, $two=false, $many=false) {

    if (!$two && !$many){
        list($one, $two, $many) = explode('|', $one);
    }

	if (mb_strstr($num, '.')){
		return $two;
	}

    if ($num%10==1 && $num%100!=11){
        return $one;
    }
    elseif($num%10>=2 && $num%10<=4 && ($num%100<10 || $num%100>=20)){
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
 * Строит рекурсивно список UL из массива
 *
 * @author acmol
 *
 * @param array $array
 * @return string
 */
function html_array_to_list($array){

    $html = '<ul>' . "\n";

    foreach($array as $key => $elem){

        if(!is_array($elem)){
            $html = $html . '<li>'.$elem.'</li>' . "\n";
        }
        else {
            $html = $html . '<li class="folder">'.$key.' '.html_array_to_list($elem).'</li>' . "\n";
        }

    }

    $html = $html . "</ul>" . "\n";

    return $html;

}

function html_tags_bar($tags){

    if (!$tags) { return ''; }

    if (!is_array($tags)){
        $tags = explode(',', $tags);
    }

    foreach($tags as $id=>$tag){
        $tag = trim($tag);
        $tags[$id] = '<a href="'.href_to('tags', 'search').'?q='.urlencode($tag).'">'.$tag.'</a>';
    }

    $tags_bar = implode(', ', $tags);

    return $tags_bar;

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

function nf($number, $decimals=2){
    if (!$number) { return 0; }
    return number_format((double)str_replace(',', '.', $number), $decimals, '.', '');
}
