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
 * Удаляет файл и его родительские директории
 * @param string $file_path Отностительный или полный путь к файлу
 * @param integer $delete_parent_dir Количество родительских директорий, которые нужно также удалить, если они пустые
 * @return boolean
 */
function files_delete_file($file_path, $delete_parent_dir = 0) {

    if(!is_file($file_path)){
        $file_path = cmsConfig::get('upload_path') . $file_path;
    }

    $success = @unlink($file_path);

    if($delete_parent_dir && $success){

        $parent_dir = pathinfo($file_path, PATHINFO_DIRNAME);

        for ($i = 1; $i <= $delete_parent_dir; $i++) {

            if(!@rmdir($parent_dir)){ break; }

            $parent_dir = pathinfo($parent_dir, PATHINFO_DIRNAME);

        }

    }

    return $success;

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
        return round(($bytes / $gb), 1, PHP_ROUND_HALF_UP). ' ' . LANG_GB;
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
 * Возвращает 32-х символьный хэш, привязанный к ip адресу
 * используется для защиты от хотлинка
 *
 * @param string $file_path Путь к файлу
 * @return string
 */
function files_user_file_hash($file_path = ''){
    return md5(cmsUser::getIp().md5($file_path.cmsConfig::get('root_path')));
}

/**
 * Очищает имя файла от специальных символов
 *
 * @param string $filename
 * @return string
 */
function files_sanitize_name($filename){

	$path_parts = pathinfo($filename);
    $filename = lang_slug($path_parts['filename']) . '.' . (isset($path_parts['extension']) ? $path_parts['extension'] : '');
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

            if(strpos($url, 'https') === 0){
                curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
                curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            }
            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_HEADER, false);
            curl_setopt($curl, CURLOPT_TIMEOUT, 5);
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
    if(strpos($url, 'https') === 0){
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    }
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
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

    $image_params = img_get_params($src);
    if ($image_params === false) { return false; }

    $new_width  = $image_params['width'];
    $new_height = $image_params['height'];

    // Определяем исходный формат по MIME-информации, предоставленной
    // функцией getimagesize, и выбираем соответствующую формату
    // imagecreatefrom-функцию.
    $format = strtolower(substr($image_params['mime'], strpos($image_params['mime'], '/') + 1));
    $icfunc = 'imagecreatefrom'.$format;
    $igfunc = 'image'.$format;

    if (!function_exists($icfunc)) { return false; }
    if (!function_exists($igfunc)) { return false; }

    if (($new_height <= $maxheight) && ($new_width <= $maxwidth)) {
        return copy($src, $dest);
    }

    $isrc = $icfunc($src);

    // автоповорот изображений
    if(isset($image_params['exif']['orientation'])) {
        $actions = array();
        switch ($image_params['exif']['orientation']) {
            case 1: break;
            case 2: $actions = array('img_flip' => 'x'); break;
            case 3: $actions = array('img_rotate' => -180); break;
            case 4: $actions = array('img_flip' => 'y'); break;
            case 5: $actions = array('img_flip' => 'y', 'img_rotate' => 90); break;
            case 6: $actions = array('img_rotate' => 90); break;
            case 7: $actions = array('img_flip' => 'x', 'img_rotate' => 90); break;
            case 8: $actions = array('img_rotate' => -90); break;
        }
        if($actions){
            foreach ($actions as $orient_func => $func_param) {

                $orient_result = $orient_func($func_param, $isrc, $new_width, $new_height);

                $isrc       = $orient_result['image_res'];
                $new_width  = $image_params['width'] = $orient_result['width'];
                $new_height = $image_params['height'] = $orient_result['height'];

            }
        }
    }

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

        if(!$maxwidth || !$maxheight){

            $ratio = $new_height / $new_width;

            if(!$maxwidth){

                $new_height = min($maxheight, $new_height);
                $new_width  = $new_height / $ratio;

            } else {

                $new_width  = min($maxwidth, $new_width);
                $new_height = $new_width * $ratio;

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

        imagecopyresampled($idest, $isrc, 0, 0, 0, 0, $new_width, $new_height, $image_params['width'], $image_params['height']);

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

    $exif_data = array();
    $exif = (function_exists('exif_read_data') && $s['mime'] === 'image/jpeg' ? (@exif_read_data($path, null, true)) : null);
    if($exif){
        if(isset($exif['COMPUTED']['ApertureFNumber'])){
            $exif_data['aperturefnumber'] = $exif['COMPUTED']['ApertureFNumber'];
        } elseif(isset($exif['EXIF']['FNumber'])){
            $num = explode('/', $exif['EXIF']['FNumber']);
            $exif_data['aperturefnumber'] = 'f/'.($num[0]/$num[1]);
        }
        if(isset($exif['EXIF']['ExposureTime'])){
            $exif_data['exposuretime'] = $exif['EXIF']['ExposureTime'];
        } elseif(isset($exif['IFD0']['ExposureTime'])){
            $exif_data['exposuretime'] = $exif['IFD0']['ExposureTime'];
        }
        if(isset($exif['IFD0']['Make'])){
            $exif_data['camera'] = $exif['IFD0']['Make'];
        }
        if(isset($exif['IFD0']['Model'])){
            $exif_data['camera'] = $exif['IFD0']['Model'];
        }
        if(isset($exif['IFD0']['DateTime'])){
            $exif_data['date'] = $exif['IFD0']['DateTime'];
        } elseif(isset($exif['EXIF']['DateTimeOriginal'])){
            $exif_data['date'] = $exif['EXIF']['DateTimeOriginal'];
        } elseif(isset($exif['EXIF']['DateTimeDigitized'])){
            $exif_data['date'] = $exif['EXIF']['DateTimeDigitized'];
        }
        if(isset($exif['EXIF']['ISOSpeedRatings'])){
            $exif_data['isospeedratings'] = $exif['EXIF']['ISOSpeedRatings'];
            if(is_array($exif_data['isospeedratings'])){
                $exif_data['isospeedratings'] = current($exif_data['isospeedratings']);
            }
        }
        if(isset($exif['EXIF']['FocalLength'])){
            $exif_data['focallength'] = $exif['EXIF']['FocalLength'];
        }
        if(isset($exif['IFD0']['Orientation'])){
            $exif_data['orientation'] = $exif['IFD0']['Orientation'];
        }
    }

    $orientation = 'square';
    if($s[0] > $s[1]){
        $orientation = 'landscape';
    }
    if($s[0] < $s[1]){
        $orientation = 'portrait';
    }

    return array(
        'orientation' => $orientation,
        'width'       => $s[0],
        'height'      => $s[1],
        'mime'        => $s['mime'],
        'exif'        => $exif_data,
        'filesize'    => round(filesize($path))
    );

}
function img_flip($direction, $image_res, $width, $height) {

    $new_image_res = imagecreatetruecolor($width, $height);

    imagealphablending($new_image_res, false);
    imagesavealpha($new_image_res, true);

    switch (strtolower($direction)) {
        case 'y':
            for ($y = 0; $y < $height; $y++) {
                imagecopy($new_image_res, $image_res, 0, $y, 0, $height - $y - 1, $width, 1);
            }
            break;
        default:
            for ($x = 0; $x < $width; $x++) {
                imagecopy($new_image_res, $image_res, $x, 0, $width - $x - 1, 0, 1, $height);
            }
            break;
    }

    return array(
        'width'     => $width,
        'height'    => $height,
        'image_res' => $new_image_res
    );

}
function img_rotate($angle, $image_res) {

    if ($angle < -360) {
        $angle = -360;
    } else if ($angle > 360) {
        $angle = 360;
    }

    $new_image_res = imagerotate($image_res, -$angle, 0);

    return array(
        'width'     => imagesx($new_image_res),
        'height'    => imagesy($new_image_res),
        'image_res' => $new_image_res
    );

}
