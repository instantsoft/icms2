<?php

/**
 * Рекурсивно удаляет директорию
 * @param string $directory
 * @param bool $is_clear Если TRUE, то директория будет очищена, но не удалена
 * @return bool
 */
function files_remove_directory($directory, $is_clear=false){

    if(substr($directory,-1) == '/'){
        $directory = substr($directory,0,-1);
    }

    if(!file_exists($directory) || !is_dir($directory) || !is_readable($directory)){
        return false;
    }

    $handle = opendir($directory);

    while (false !== ($node = readdir($handle))){

        if($node != '.' && $node != '..'){

            $path = $directory.'/'.$node;

            if(is_dir($path)){
                if (!files_remove_directory($path)) { return false; }
            } else {
                if(!@unlink($path)) { return false; }
            }

        }

    }

    closedir($handle);

    if ($is_clear == false){
        if(!@rmdir($directory)){
            return false;
        }
    }

    return true;

}

/**
 * Очищает директорию
 * @param string $directory
 * @return bool
 */
function files_clear_directory($directory){
    return files_remove_directory($directory, true);
}

/**
 * Возвращает дерево каталогов и файлов по указанному пути в виде
 * рекурсивного массива
 * @param string $path
 * @return array
 */
function files_tree_to_array($path){

    $data = array();

    $dir = new DirectoryIterator( $path );

    foreach ( $dir as $node ){
        if ( $node->isDir() && !$node->isDot() ){
            $data[$node->getFilename()] = files_tree_to_array( $node->getPathname() );
        } else if ( $node->isFile() ){
            $data[] = $node->getFilename();
        }
    }

    return $data;

}

/**
 * Нормализует путь к файлу, убирая все условные переходы.
 *
 * Например путь
 *      /path/to/../folder
 * будет преобразован в
 *      /path/folder
 *
 * @param string $path
 * @return string
 */
function files_normalize_path($path) {

  $parts = explode('/', $path);
  $safe = array();
  foreach ($parts as $idx => $part) {
    if (empty($part) || ('.' == $part)) {
      continue;
    } elseif ('..' == $part) {
      array_pop($safe);
      continue;
    } else {
      $safe[] = $part;
    }
  }

  $path = implode('/', $safe);
  return $path;

}

/**
 * Получает строку вида "8M" или "1024K" и возвращает значение в байтах
 * Полезно при получении max_upload_size из php.ini
 *
 * @param string $value
 * @return int
 */
function files_convert_bytes($value) {
    if ( is_numeric( $value ) ) {
        return $value;
    } else {
        $value_length = strlen( $value );
        $qty = substr( $value, 0, $value_length - 1 );
        $unit = strtolower( substr( $value, $value_length - 1 ) );
        switch ( $unit ) {
            case 'k':
                $qty *= 1024;
                break;
            case 'm':
                $qty *= 1048576;
                break;
            case 'g':
                $qty *= 1073741824;
                break;
        }
        return $qty;
    }
    return $value;
}

/**
 * Переводит байты в Гб, Мб или Кб и возвращает полученное число + единицу измерения
 * в виде единой строки
 * @param int $bytes
 * @return string
 */
function files_format_bytes($bytes) {

    $kb = 1024;
    $mb = 1048576;
    $gb = 1073741824;

    if (round($bytes / $gb) > 0) {
        return ceil($bytes / $gb) . ' ' . LANG_GB;
    }

    if (round($bytes / $mb) > 0) {
        return ceil($bytes / $mb) . ' ' . LANG_MB;
    }

    if (round($bytes / $kb) > 0) {
        return ceil($bytes / $kb) . ' ' . LANG_KB;
    }

    return $bytes . ' ' . LANG_B;

}

/**
 * Очищает имя файла от специальных символов
 *
 * @param string $filename
 * @return string
 */
function files_sanitize_name($filename){

	$path_parts = pathinfo($filename);
    $filename = lang_slug($path_parts['filename']) . '.' . $path_parts['extension'];
    $filename = mb_strtolower($filename);
    $filename = preg_replace(array('/[\&]/', '/[\@]/', '/[\#]/'), array('-and-', '-at-', '-number-'), $filename);
    $filename = preg_replace('/[^(\x20-\x7F)]*/','', $filename);
    $filename = str_replace(' ', '-', $filename);
    $filename = str_replace('\'', '', $filename);
    $filename = preg_replace('/[^\w\-\.]+/', '', $filename);
    $filename = preg_replace('/[\-]+/', '-', $filename);

	return $filename;

}

/**
 * Получает данные по заданному url
 * @param string $url URL, откуда нужно получить данные
 * @return string
 */
function file_get_contents_from_url($url){

    $data = @file_get_contents($url);

    if ($data===false){

        if (function_exists('curl_init')){

            $curl = curl_init();

            if(strpos($url, 'https') !== false){
                curl_setopt(CURLOPT_SSL_VERIFYHOST, 0);
                curl_setopt(CURLOPT_SSL_VERIFYPEER, false);
            }
            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_HEADER, false);
            curl_setopt($curl, CURLOPT_TIMEOUT, 3);
            $data = curl_exec($curl);
            curl_close($curl);

        }

    }

    return $data;

}

/**
 * Сохраняет удаленно расположенный файл
 * @param string $url url файла
 * @param string $destination Полный путь куда сохраненить файл
 * @return boolean
 */
function file_save_from_url($url, $destination){

    if (!function_exists('curl_init')){ return false; }

    $dest_file = @fopen($destination, "w");

    $curl = curl_init();
    if(strpos($url, 'https') !== false){
        curl_setopt(CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt(CURLOPT_SSL_VERIFYPEER, false);
    }
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_FILE, $dest_file);
    curl_setopt($curl, CURLOPT_HEADER, false);
    curl_exec($curl);
    curl_close($curl);
    fclose($dest_file);

    return true;

}
/**
 * Накладывает ваттермарк на изображение
 * @param string $src_file Путь (относительно папки upload) к файлу, на который нужно наложить ватермарк
 * @param string $wm_file Путь (относительно папки upload) к файлу ватермарка
 * @param string $wm_origin Позиция ватермарка: top-left|top|top-right|left|center|right|bottom-left|bottom|bottom-right
 * @param int $wm_margin Отступы от края изображения в px
 * @param int $quality Качество результирующего изображения от 1 до 100
 * @return boolean
 */
function img_add_watermark($src_file, $wm_file, $wm_origin, $wm_margin, $quality=90){

    $config = cmsConfig::getInstance();

    $src_file = $config->upload_path.$src_file;
    $wm_file  = $config->upload_path.$wm_file;

    //
    // Основное изображение
    //
    $img_size = getimagesize($src_file);
    if ($img_size === false) { return false; }

    $format = strtolower(substr($img_size['mime'], strpos($img_size['mime'], '/') + 1));
    $icfunc = 'imagecreatefrom'.$format;
    $igfunc = 'image'.$format;

    if (!function_exists($icfunc)) { return false; }
    if (!function_exists($igfunc)) { return false; }

    $img_width  = $img_size[0];
    $img_height = $img_size[1];

    $img = $icfunc($src_file);

    if ($format == 'png' || $format == 'gif') {
        imagealphablending($img, true);
        imagesavealpha($img, true);
    }

    //
    // Ватермарк
    //
    $wm_size = getimagesize($wm_file);
    if ($wm_size === false) { return false; }

    $wm_width  = $wm_size[0];
    $wm_height = $wm_size[1];

    $wm_format = strtolower(substr($wm_size['mime'], strpos($wm_size['mime'], '/' ) + 1));
    $wm_func   = 'imagecreatefrom'.$wm_format;
    if (!function_exists($wm_func)) { return false; }

    $wm = $wm_func($wm_file);

    if (!$wm_margin) { $wm_margin = 0; }

    $x = 0; $y = 0;

    switch($wm_origin){
        case 'top-left':
            $x = $wm_margin;
            $y = $wm_margin;
            break;
        case 'top':
            $x = ($img_width/2) - ($wm_width/2);
            $y = $wm_margin;
            break;
        case 'top-right':
            $x = ($img_width - $wm_width - $wm_margin);
            $y = $wm_margin;
            break;
        case 'left':
            $x = $wm_margin;
            $y = ($img_height/2) - ($wm_height/2);
            break;
        case 'center':
            $x = ($img_width/2) - ($wm_width/2);
            $y = ($img_height/2) - ($wm_height/2);
            break;
        case 'right':
            $x = ($img_width - $wm_width - $wm_margin);
            $y = ($img_height/2) - ($wm_height/2);
            break;
        case 'bottom-left':
            $x = $wm_margin;
            $y = ($img_height - $wm_height - $wm_margin);
            break;
        case 'bottom':
            $x = ($img_width/2) - ($wm_width/2);
            $y = ($img_height - $wm_height - $wm_margin);
            break;
        case 'bottom-right':
            $x = ($img_width - $wm_width - $wm_margin);
            $y = ($img_height - $wm_height - $wm_margin);
            break;
    }

    imagecopyresampled($img, $wm, $x, $y, 0, 0, $wm_width, $wm_height, $wm_width, $wm_height);

    if ($format == 'jpeg') {
        imageinterlace($img, 1);
    }

    if ($format == 'png') {
        $quality = (10 - ceil($quality / 10));
    }
    if ($format == 'gif') {
        $quality = NULL;
    }

    $igfunc($img, $src_file, $quality);

    imagedestroy($img);
    imagedestroy($wm);

    return true;

}
/**
 * Изменяет размер изображения $src, сохраняя измененное в $dest
 * @param string $src Полный путь к исходному изображению
 * @param string $dest Полный путь куда сохранять измененное изображение
 * @param int $maxwidth Максимальная ширина в px
 * @param int $maxheight Максимальная высота в px
 * @param bool $is_square Создавать квадратное изображение
 * @param int $quality Качество результирующего изображения от 1 до 100
 * @return boolean
 */
function img_resize($src, $dest, $maxwidth, $maxheight=160, $is_square=false, $quality=95){

    if (!file_exists($src)) { return false; }

    $upload_dir = dirname($dest);

    if (!is_writable($upload_dir)) {

        @chmod($upload_dir, 0777);

        if (!is_writable($upload_dir)) {
            return false;
        }

    }

    $size = getimagesize($src);
    if ($size === false) { return false; }

    $new_width  = $size[0];
    $new_height = $size[1];

    // Определяем исходный формат по MIME-информации, предоставленной
    // функцией getimagesize, и выбираем соответствующую формату
    // imagecreatefrom-функцию.
    $format = strtolower(substr($size['mime'], strpos($size['mime'], '/') + 1));
    $icfunc = 'imagecreatefrom'.$format;
    $igfunc = 'image'.$format;

    if (!function_exists($icfunc)) { return false; }
    if (!function_exists($igfunc)) { return false; }

    if (($new_height <= $maxheight) && ($new_width <= $maxwidth)) {
        return copy($src, $dest);
    }

    $isrc = $icfunc($src);

    if ($is_square) {

        $idest = imagecreatetruecolor($maxwidth, $maxwidth);

        if ($format == 'jpeg') {

            imagefill($idest, 0, 0, 0xFFFFFF);

        } else if ($format == 'png' || $format == 'gif') {

            $trans = imagecolorallocatealpha($idest, 255, 255, 255, 127);
            imagefill($idest, 0, 0, $trans);
            imagealphablending($idest, true);
            imagesavealpha($idest, true);

        }

        // вырезаем квадратную серединку по x, если фото горизонтальное
        if ($new_width > $new_height) {

            imagecopyresampled($idest, $isrc, 0, 0, round(( max($new_width, $new_height) - min($new_width, $new_height) ) / 2), 0, $maxwidth, $maxwidth, min($new_width, $new_height), min($new_width, $new_height));

        }

        // вырезаем квадратную верхушку по y,
        if ($new_width < $new_height) {
            imagecopyresampled($idest, $isrc, 0, 0, 0, 0, $maxwidth, $maxwidth, min($new_width, $new_height), min($new_width, $new_height));
        }

        // квадратная картинка масштабируется без вырезок
        if ($new_width == $new_height) {
            imagecopyresampled($idest, $isrc, 0, 0, 0, 0, $maxwidth, $maxwidth, $new_width, $new_width);
        }

    } else {

        if ($new_width > $maxwidth) {

            $wscale = $maxwidth / $new_width;

            $new_width  *= $wscale;
            $new_height *= $wscale;

        }

        if ($new_height > $maxheight) {

            $hscale = $maxheight / $new_height;

            $new_width  *= $hscale;
            $new_height *= $hscale;

        }

        $idest = imagecreatetruecolor($new_width, $new_height);

        if ($format == 'jpeg') {

            imagefill($idest, 0, 0, 0xFFFFFF);

        } else if ($format == 'png' || $format == 'gif') {

            $trans = imagecolorallocatealpha($idest, 255, 255, 255, 127);
            imagefill($idest, 0, 0, $trans);
            imagealphablending($idest, true);
            imagesavealpha($idest, true);

        }

        imagecopyresampled($idest, $isrc, 0, 0, 0, 0, $new_width, $new_height, $size[0], $size[1]);

    }

    if ($format == 'jpeg') {
        imageinterlace($idest, 1);
    }

    if ($format == 'png') {
        $quality = (10 - ceil($quality / 10));
    }
    if ($format == 'gif') {
        $quality = NULL;
    }

    // вывод картинки и очистка памяти
    $igfunc($idest, $dest, $quality);

    imagedestroy($isrc);
    imagedestroy($idest);

    return true;

}
/**
 * Возвращает параметры изображения
 * @param string $path Полный путь к файлу
 * @return boolean|array
 */
function img_get_params($path){
    $s = getimagesize($path);
    if ($s === false) { return false; }
    return array(
        'width'=>$s[0],
        'height'=>$s[1],
        'mime'=>$s['mime'],
        'filesize'=>round(filesize($path))
    );
}