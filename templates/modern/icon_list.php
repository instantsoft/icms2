<?php
/**
 * Массив иконок шаблона
 * Для удобного просмотра и выбора, например в меню
 */

$list = [];

$files = glob(dirname(__FILE__).'/images/icons/*.svg');

if (!$files) {
    return $list;
}

foreach ($files as $file_path) {

    $matches = [];

    $icon_svg_data = file_get_contents($file_path);

    if(!$icon_svg_data){
        continue;
    }

    preg_match_all('#<symbol id="([^"]+)"#', $icon_svg_data, $matches);

    if(empty($matches[1])){
        continue;
    }

    $list[pathinfo($file_path, PATHINFO_FILENAME)] = $matches[1];
}

$icon_list = [];

foreach ($list as $file_name => $names) {

    foreach ($names as $name) {
        $icon_list[$file_name][] = [
            'title' => $name,
            'name'  => $file_name.':'.$name,
            'html'  => html_svg_icon($file_name, $name, 16, false)
        ];
    }
}

return $icon_list;
