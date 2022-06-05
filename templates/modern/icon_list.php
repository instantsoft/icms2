<?php
/**
 * Массив иконок шаблона Modern и его наследуемых
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
$files_modern = [];
$files_current = [];
$modern_template_icons_path = '';

// Если шаблон не Modern, а зависимый от него, ищем иконки сначала в Modern
if($this->name !== 'modern'){

    if(array_search('modern', $this->getInheritNames(), true) !== false) {

        $modern_template_icons_path = $this->site_config->root_path . self::TEMPLATE_BASE_PATH . 'modern/images/icons/';

        $files_modern = glob($modern_template_icons_path.'*.svg');

        if (!$files_modern) {
            $files_modern = [];
        }
    }
}

$template_icons_path = $this->getTplFilePath('images/icons/');

if($template_icons_path !== $modern_template_icons_path){

    $files_current = glob($template_icons_path.'*.svg');

    if (!$files_current) {
        $files_current = [];
    }
}

$files = array_unique(array_merge($files_modern, $files_current));

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

    $list[pathinfo($file_path, PATHINFO_FILENAME)] = [
        'url'   => str_replace($this->site_config->root_path, $this->site_config->root, $file_path),
        'names' => $matches[1]
    ];
}

$icon_list = [];

foreach ($list as $file_name => $item) {

    foreach ($item['names'] as $name) {
        $icon_list[$file_name][] = [
            'title' => $name,
            'name'  => $file_name.':'.$name,
            'html'  => '<svg class="icms-svg-icon w-16" fill="currentColor"><use href="'.$item['url'].'#'.$name.'"></use></svg>'
        ];
    }
}

return $icon_list;
