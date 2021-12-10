<?php
/**
 * Массив иконок шаблона
 * Для удобного просмотра и выбора, например в меню
 *
 * $this - контекст cmsTemplate, где это объект шаблона,
 * для которого ищем иконки
 *
 * Наличие в шаблоне директории /images/icons/ говорит о том, что
 * именно здесь мы ищем файлы спрайтов иконок
 *
 * Наследование шаблонов работает. В данном случае с шаблонами,
 * наследуемыми от modern, этот файл копировать в свой шаблон нет необходимости
 */

$list = [];

$template_icons_path = $this->getTplFilePath('images/icons/');

$files = glob($template_icons_path.'*.svg');

if (!$files) {
    return $list;
}

$template_path = str_replace(
        $this->site_config->root_path,
        $this->site_config->root,
        $template_icons_path
    );

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
            'html'  => '<svg class="icms-svg-icon w-16" fill="currentColor"><use href="'.$template_path.$file_name.'.svg#'.$name.'"></use></svg>'
        ];
    }
}

return $icon_list;
