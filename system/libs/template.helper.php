<?php
/**
 * Выводит инлайновую svg иконку
 * @staticvar string $template_path
 * @param string $file Имя файла SVG спрайта в templates/NAME/images/icons/
 * @param string $name Имя иконки
 * @param integer $size Размер
 * @param boolean $print Печатать или возвращать строку
 */
function html_svg_icon($file, $name, $size = 16, $print = true){
    static $template_path = null;
    if(!isset($template_path)){
        $template_path = cmsTemplate::getInstance()->getTemplateFilePath('images/icons/', true);
    }
	$icon = '<svg class="icms-svg-icon w-'.$size.'" fill="currentColor"><use xlink:href="'.$template_path.$file.'.svg#'.$name.'"></use></svg>';
    if($print){
        echo $icon;
    } else {
        return $icon;
    }
}
/**
 * Выводит тег <a>
 * @param string $title Название
 * @param string $href Ссылка
 */
function html_link($title, $href){
	echo '<a href="'.html($href, false).'">'.html($title, false).'</a>';
}

/**
 * Возвращает панель со страницами
 *
 * @param integer $page Текущая страница
 * @param integer $perpage Записей на одной странице
 * @param integer $total Количество записей
 * @param string|array $base_uri Базовый URL, может быть массивом из элементов first и base
 * @param array $query Массив параметров запроса
 * @param string $page_param_name Название параметра номера страницы
 * @return string
 */
function html_pagebar($page, $perpage, $total, $base_uri = false, $query = [], $page_param_name = 'page') {

    if (!$total || $total <= $perpage){ return ''; }

    $paginator = new cmsPaginator($total, $perpage, $page, $base_uri, $query);

    if($page_param_name){
        $paginator->setPageParamName($page_param_name);
    }

    return $paginator->getRendered();
}

/**
 * Возвращает тег <input>
 * @param string $type Тип поля
 * @param string $name Имя поля
 * @param string $value Значение по умолчанию
 * @param array $attributes Атрибуты тега название=>значение
 * @return html
 */
function html_input($type='text', $name='', $value='', $attributes=array()){
    if ($type=='password'){ $attributes['autocomplete'] = 'off'; }
    $attr_str = html_attr_str($attributes);
    $class = 'input';
    if (isset($attributes['class'])) { $class .= ' '.$attributes['class']; }
	return '<input type="'.$type.'" class="form-control '.$class.'" name="'.$name.'" value="'.html($value, false).'" '.$attr_str.'/>';
}

function html_file_input($name, $attributes=array()){
    $attr_str = html_attr_str($attributes);
    $class = 'file-input';
    if (isset($attributes['class'])) { $class .= ' '.$attributes['class']; }
	return '<input type="file" class="form-control-file '.$class.'" name="'.$name.'" '.$attr_str.'/>';
}

function html_textarea($name='', $value='', $attributes=array()){
    $attr_str = html_attr_str($attributes);
    $class = 'textarea';
    if (isset($attributes['class'])) { $class .= ' '.$attributes['class']; }
	$html = '<textarea name="'.$name.'" class="form-control '.$class.'" '.$attr_str.'>'.html($value, false).'</textarea>';
	return $html;
}

function html_back_button(){
	return '<div class="back_button"><a href="javascript:window.history.go(-1);">'.LANG_BACK.'</a></div>';
}

function html_checkbox($name, $checked=false, $value=1, $attributes=array()){
    if ($checked) { $attributes['checked'] = 'checked'; }
    $attr_str = html_attr_str($attributes);
    $class = 'input-checkbox';
    if (isset($attributes['class'])) { $class .= ' '.$attributes['class']; }
	return '<input type="checkbox" class="form-check-input '.$class.'" name="'.$name.'" value="'.$value.'" '.$attr_str.'/>';
}

function html_radio($name, $checked=false, $value=1, $attributes=array()){
    if ($checked) { $attributes['checked'] = 'checked'; }
    $attr_str = html_attr_str($attributes);
    $class = 'input-radio';
    if (isset($attributes['class'])) { $class .= ' '.$attributes['class']; }
  return '<input type="radio" class="form-check-input '.$class.'" name="'.$name.'" value="'.$value.'" '.$attr_str.'/>';
}

function html_date($date=false, $is_time=false){
    $timestamp = $date ? strtotime($date) : time();
    $date_format = cmsConfig::get('date_format');
    $date = '<time datetime="'.date('c', $timestamp).'">'.htmlspecialchars(($date_format == 'd F Y') ? string_date_format($timestamp) : date(cmsConfig::get('date_format'), $timestamp)).'</time>';
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

function html_datepicker($name='', $value='', $attributes=array(), $datepicker = array()){
    if (isset($attributes['id'])){
        $id = $attributes['id'];
        unset($attributes['id']);
    } else {
        $id = $name;
    }
    $attributes['autocomplete'] = 'off';
    $datepicker_default = array(
        'showStatus' => true,
        'changeYear' => true,
        'yearRange'  => '1976:'.date('Y', strtotime('+5 year')),
        'dateFormat' => cmsConfig::get('date_format_js')
    );
    if($datepicker){
        $datepicker_default = array_merge($datepicker_default, $datepicker);
    }
    $class = 'form-control date-input';
    if (isset($attributes['class'])) { $class .= ' '.$attributes['class']; }
    $attr_str = html_attr_str($attributes);
	$html  = '<input type="text" placeholder="'.LANG_SELECT.'" name="'.$name.'" value="'.htmlspecialchars($value).'" class="'.$class.'"  id="'.$id.'" '.$attr_str.'/>';
    $script = '<script>';
    $script .= '$(function(){ var datepicker_params = '.json_encode($datepicker_default).';datepicker_params.onSelect = function(dateText,inst){$("#'.$id.'").trigger("input");icms.events.run("icms_datepicker_selected_'.$name.'", inst);};datepicker_params.beforeShow = function(input,inst){icms.events.run("icms_datepicker_before_show_'.$name.'", inst);};$("#'.$id.'").datepicker(datepicker_params);});';
    $script .= '</script>';
    if(cmsCore::getInstance()->request->isAjax()){
        $html .= $script;
    } else {
        cmsTemplate::getInstance()->addBottom($script);
    }
    return $html;
}

/**
 * Возвращает кнопку "Отправить" type="submit"
 *
 * @param string $caption Заголовок кнопки
 * @param string $name Имя (name)
 * @param array $attributes Массив параметров
 * @return string
 */
function html_submit($caption = LANG_SUBMIT, $name = 'submit', $attributes = []) {

    $attributes['type'] = 'submit';

    $class = 'button-submit btn-primary';

    $attributes['class'] = !empty($attributes['class']) ? $attributes['class'].' '.$class : $class;

    return html_button($caption, $name, '', $attributes);
}

/**
 * Возвращает html-код кнопки
 * @param str $caption Заголовок
 * @param str $name Название кнопки
 * @param str $onclick Содержимое аттрибута onclick (javascript)
 * @return html
 */
function html_button($caption, $name, $onclick = '', $attributes = []) {

    if (!isset($attributes['type'])) { $attributes['type'] = 'button'; }

    $attr_str = html_attr_str($attributes);

    $class = 'button btn';

    if (!empty($attributes['class'])) { $class .= ' '.$attributes['class']; }
    else { $class .= ' btn-secondary'; }

	return '<button value="'.html($caption, false).'" class="'.$class.'" name="'.$name.'" onclick="'.html($onclick, false).'" '.$attr_str.'><span>'.html($caption, false).'</span></button>';
}

/**
 * Возвращает тег <img> аватара пользователя
 * @param array|yaml $avatars Все изображения аватара
 * @param string $size_preset Название пресета
 * @param string $alt Замещающий текст изображения
 * @param bool $is_html_empty_avatar Вместо дефолтных изображений показывать цветной блок с буквой
 * @return string
 */
function html_avatar_image($avatars, $size_preset='small', $alt='', $is_html_empty_avatar=false){

    $src = html_avatar_image_src($avatars, $size_preset);

    $img = '<img class="img-fluid" src="'.$src.'" alt="'.html($alt, false).'" title="'.html($alt, false).'" />';

    if(empty($avatars) && !empty($alt) && $is_html_empty_avatar){

        $iparams = get_image_block_param_by_title($alt);

        $img = '<div class="default_avatar" style="'.$iparams['style'].'" data-letter="'.htmlspecialchars(mb_substr($alt, 0, 1)).'">'.$img.'</div>';

    }

    return $img;

}

function html_avatar_image_empty($title, $class = ''){

    $iparams = get_image_block_param_by_title($title);

    return '<div class="icms-profile-avatar__default '.$class.'" style="'.$iparams['style'].'"><svg fill="currentColor" viewBox="0 0 28 21"><text x="50%" y="50%" dominant-baseline="central" text-anchor="middle">'.mb_strtoupper(htmlspecialchars(mb_substr($title, 0, 1))).'</text></svg></div>';
}

function get_image_block_param_by_title($title) {

    static $image_block_params = null;
    if(isset($image_block_params[$title])){
        return $image_block_params[$title];
    }

    $bg_color = substr(dechex(crc32($title)), 0, 6);

    // выбираем контрастный цвет для текста
    $r = max( hexdec( substr($bg_color, 0, 2) ), 90);
    $g = max( hexdec( substr($bg_color, 2, 2) ), 90);
    $b = max( hexdec( substr($bg_color, 4, 2) ), 90);
    $yiq = (($r*299)+($g*587)+($b*114)) / 1000;
    $txt_color = ($yiq >= 140) ? 'black' : 'white';

    $image_block_params[$title] = array(
        'style' => "background-color: rgba({$r}, {$g}, {$b}, .9); color: {$txt_color};"
    );

    return $image_block_params[$title];

}

/**
 * Возвращает тег <img>
 * @param array|yaml $image Все размеры заданного изображения
 * @param string $size_preset Название пресета
 * @param string $alt Замещающий текст изображения
 * @param array $attributes Массив аттрибутов тега
 * @return string
 */
function html_image($image, $size_preset='small', $alt='', $attributes = array()){

    if(is_array($size_preset)){
        list($small_preset, $modal_preset) = $size_preset;
    } else {
        $small_preset = $size_preset;
        $modal_preset = false;
    }

	$src = html_image_src($image, $small_preset, true);
	if (!$src) { return ''; }

    $title = html((isset($attributes['title']) ? $attributes['title'] : $alt), false); unset($attributes['title']);

    $attr_str = html_attr_str($attributes);
    $class = isset($attributes['class']) ? $attributes['class'] : '';

    $image_html = '<img src="'.$src.'" title="'.$title.'" alt="'.html($alt, false).'" '.$attr_str.' class="img-fluid '.$class.'" />';

    if($modal_preset){
        $modal_src = html_image_src($image, $modal_preset, true);
        if ($modal_src) {
            return '<a title="'.$title.'" class="ajax-modal modal_image hover_image" href="'.$modal_src.'">'.$image_html.'</a>';
        }
    }

    return $image_html;

}

/**
 * Возвращает тег HTML gif изображения
 * @param array|yaml $image Все размеры заданного изображения
 * @param string $size_preset Название пресета
 * @param string $alt Замещающий текст изображения
 * @param array $attributes Массив аттрибутов тега
 * @return string
 */
function html_gif_image($image, $size_preset='small', $alt='', $attributes = array()){

    if(is_array($size_preset)){
        list($small_preset, $modal_preset) = $size_preset;
    } else {
        $small_preset = $size_preset;
        $modal_preset = false;
    }

    $class = isset($attributes['class']) ? $attributes['class'] : '';
    if($small_preset == 'micro'){
        $class .= ' micro_image';
    }

    $original_src = html_image_src($image, $modal_preset?:'original', true);
    $preview_src  = html_image_src($image, $small_preset, true);

    if (!$preview_src) { return ''; }

    return '<a class="ajax-modal gif_image '.$class.'" href="'.$original_src.'" '.html_attr_str($attributes).'>
                <span class="background_overlay"></span>
                <span class="image_label">gif</span>
                <img class="img-fluid" src="'.$preview_src.'" alt="'.html($alt, false).'" />
            </a>';

}

/**
 * Генерирует список опций
 * @param string $name Имя списка
 * @param array $items Массив элементов списка (значение => заголовок)
 * @param string|array $selected Значение выбранного(ых) элемента
 * @param array $attributes Массив аттрибутов тега
 * @return string HTML
 */
function html_select($name, $items, $selected = '', $attributes = array()){

    $name = isset($attributes['multiple']) ? $name . '[]' : $name;

    $attr_str = html_attr_str($attributes);
    $class = isset($attributes['class']) ? $attributes['class'] : '';
    $html = '<select class="form-control '.$class.'" name="'.$name.'" '.$attr_str.'>'."\n";

    $optgroup = false;

    if(is_array($selected) && $selected){
        foreach ($selected as $k => $v) {
            if(is_numeric($v)){ $selected[$k] = (int)$v; }
        }
    }

    if($items && is_array($items)){
        foreach($items as $value => $title){

            if(is_array($title)){
                if($optgroup !== false){
                    $html .= "\t".'</optgroup>'."\n";
                    $optgroup = false;
                }
                $optgroup = true;
                $html .= "\t".'<optgroup label="'.htmlspecialchars($title[0]).'">'."\n";
                continue;
            }

            if (is_array($selected)){
                $sel = in_array($value, $selected, true) ? 'selected' : '';
            } else {
                $sel = ((string) $selected === (string) $value) ? 'selected' : '';
            }

            $html .= "\t".'<option value="'.htmlspecialchars($value).'" '.$sel.'>'.htmlspecialchars($title).'</option>'."\n";

        }
    }

    if($optgroup !== false){
        $html .= "\t".'</optgroup>'."\n";
    }

    $html .= '</select>'."\n";
    return $html;

}

/**
 * Генерирует список опций с множественным выбором
 * @param string $name Имя списка
 * @param array $items Массив элементов списка (значение => заголовок)
 * @param string $selected Массив значений выбранных элементов
 * @param array $attributes Массив аттрибутов тега
 * @return html
 */
function html_select_multiple($name, $items, $selected=array(), $attributes=array(), $is_tree=false){
    $attr_str = html_attr_str($attributes);
    $class = isset($attributes['class']) ? $attributes['class'] : '';
	$html = '<div class="input_checkbox_list '.$class.'" '.$attr_str.'>'."\n";
    $start_level = false;
    if(is_array($selected) && $selected){
        foreach ($selected as $k => $v) {
            if(is_numeric($v)){ $selected[$k] = (int)$v; }
        }
    }
    foreach ($items as $value=>$title){

        $checked = is_array($selected) && in_array($value, $selected, true);

        if ($is_tree){

            $level = mb_strlen(str_replace(' ', '', $title)) - mb_strlen(ltrim(str_replace(' ', '', $title), '-'));

            if ($start_level === false) { $start_level = $level; }

            $level = $level - $start_level;

            $title = ltrim($title, '- ');

            $html .= "\t" . '<label class="form-check form-check-block" '. ($level>0 ? 'style="margin-left:'.($level*0.75).'rem"' : ''). '>' .
                    html_checkbox($name.'[]', $checked, $value) . ' ' .
                    '<span>'.htmlspecialchars($title).'</span></label>' . "\n";

        } else {

            $html .= "\t" . '<label class="form-check form-check-inline">' .
                    html_checkbox($name.'[]', $checked, $value) . ' ' .
                    '<span>'.htmlspecialchars($title) . '</span></label>' . "\n";

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
	$html = '<select name="category_id" id="category_id" class="combobox form-control">'."\n";
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

function html_bool_span($value, $condition, $classes = ['negative badge badge-danger', 'positive badge badge-success']){
    if ($condition){
        return '<span class="'.$classes[1].'">' . $value . '</span>';
    } else {
        return '<span class="'.$classes[0].'">' . $value . '</span>';
    }
}

/**
 * Строит рекурсивно список UL из массива
 * @author acmol
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

function html_search_bar($list, $href, $link_class = '', $glue = ', '){

    if (!$list) { return ''; }

    if (!is_array($list)){
        $list = explode(',', $list);
    }

    foreach($list as $id => $letter){
        $letter = trim($letter);
        $list[$id] = '<a class="'.$link_class.'" href="'.$href.urlencode($letter).'">'.html($letter, false).'</a>';
    }

    return implode($glue, $list);

}

function html_tags_bar($tags, $prefix = '', $class = 'tags_bar_link', $glue = ', '){
    return html_search_bar($tags, href_to('tags').'/'.($prefix ? $prefix.'/' : ''), $class, $glue);
}
