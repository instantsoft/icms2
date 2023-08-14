<?php
/**
 * Копирует директорию
 *
 * @param string $directory_from Полный путь к директории, которую копируем
 * @param string $directory_to Полный путь к директории, куда копируем
 * @return bool
 */
function files_copy_directory($directory_from, $directory_to) {

    if (!is_dir($directory_from)) {
        return false;
    }

    if (!is_dir($directory_to)) {
        mkdir($directory_to, 0755, true);
    }

    $items = new FilesystemIterator($directory_from);

    foreach ($items as $item) {

        $target = $directory_to.'/'.$item->getBasename();

        if ($item->isDir()) {

            if (!files_copy_directory($item->getPathname(), $target)) {
                return false;
            }
        }
        elseif (!copy($item->getPathname(), $target)) {
            return false;
        }
    }

    return true;
}
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
    $safe  = array();

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
 * @param string $filename Имя файла
 * @param boolean $convert_slug Транслитировать?
 * @return string
 */
function files_sanitize_name($filename, $convert_slug = true){

	$path_parts = pathinfo($filename);
    if($convert_slug){
        $filename = lang_slug($path_parts['filename']) . ((isset($path_parts['extension']) ?  '.' . $path_parts['extension'] : ''));
    } else {
        $filename = trim(strip_tags($path_parts['filename']) . ((isset($path_parts['extension']) ?  '.' . $path_parts['extension'] : '')));
    }
    $filename = mb_strtolower($filename);
    $filename = preg_replace(array('/[\&]/', '/[\@]/', '/[\#]/'), array('-and-', '-at-', '-number-'), $filename);
    $filename = str_replace(' ', '-', $filename);
    $filename = str_replace('\'', '', $filename);
    $filename = preg_replace('/[^\w\-\.]+/u', '', $filename);
    $filename = preg_replace('/[\-]+/', '-', $filename);

	return $filename;
}

/**
 * Возвращает/создаёт путь к директории хранения
 *
 * @param integer $user_id
 * @return string
 */
function files_get_upload_dir($user_id = 0) {

    $dir_num_user = sprintf('%03d', intval($user_id/100));

    $file_name   = md5(microtime(true));
    $first_dir   = substr($file_name, 0, 1);
    $second_dir  = substr($file_name, 1, 1);
    $upload_path = cmsConfig::get('upload_path');

    $dest_dir = $upload_path."{$dir_num_user}/u{$user_id}/{$first_dir}/{$second_dir}/";

    if(!is_dir($dest_dir)){
        @mkdir($dest_dir, 0777, true);
        @chmod($dest_dir, 0777);
        @chmod(pathinfo($dest_dir, PATHINFO_DIRNAME), 0777);
        @chmod($upload_path . "{$dir_num_user}/u{$user_id}", 0777);
        @chmod($upload_path . "{$dir_num_user}", 0777);
    }

    return $dest_dir;

}

/**
 * Получает данные по заданному url
 * @param string $url URL, откуда нужно получить данные
 * @param integer $timeout Таймаут соединения
 * @param boolean $json_decode Преобразовывать JSON
 * @param array $params Дополнительные параметры
 * @return string
 */
function file_get_contents_from_url($url, $timeout = 5, $json_decode = false, $params = []) {

    if (!function_exists('curl_init')) {
        return null;
    }

    // По IP адресу не разрешаем
    if (preg_match('#^(?:(?:https?):\/\/)([0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}).*#ui', $url)) {
        return null;
    }

    $curl = curl_init();

    if (strpos($url, 'https') === 0) {
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    }
    $headers = ['User-Agent: Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Safari/537.36 OPR/68.0.3618.173'];
    if (!empty($params['cookie'])) {

        $cookie = [];
        foreach ($params['cookie'] as $k => $v) {
            $cookie[] = $k . '=' . $v;
        }

        $headers[] = 'Cookie: ' . implode('; ', $cookie);
        unset($params['cookie']);
    }
    if (!empty($params['proxy'])) {
        curl_setopt($curl, CURLOPT_PROXY, $params['proxy']['host'] . ':' . $params['proxy']['port']);
        curl_setopt($curl, CURLOPT_PROXYTYPE, CURLPROXY_SOCKS5);
        unset($params['proxy']);
    }
    if (!empty($params)) {
        foreach ($params as $key => $value) {
            $headers[] = $key . ': ' . $value;
        }
    }
    curl_setopt($curl, CURLOPT_PROTOCOLS, CURLPROTO_HTTPS | CURLPROTO_HTTP);
    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, $timeout);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_HEADER, false);
    curl_setopt($curl, CURLOPT_TIMEOUT, $timeout);
    $data = curl_exec($curl);
    curl_close($curl);

    if ($data === false) {
        return null;
    }

    if ($json_decode) {
        return json_decode($data, true);
    }

    return $data;
}

/**
 * Сохраняет удаленно расположенный файл
 * @param string $url url файла
 * @param string $destination Полный путь куда сохраненить файл
 * @return boolean
 */
function file_save_from_url($url, $destination) {

    if (!function_exists('curl_init')) {
        return false;
    }

    $dest_file = @fopen($destination, "w");

    $curl = curl_init();
    if (strpos($url, 'https') === 0) {
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    }
    curl_setopt($curl, CURLOPT_PROTOCOLS, CURLPROTO_HTTPS | CURLPROTO_HTTP);
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
 *
 * УСТАРЕВШАЯ ФУНКЦИЯ
 *
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
        case 'top-center':
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
 *
 * УСТАРЕВШАЯ ФУНКЦИЯ
 *
 * @param string $src Полный путь к исходному изображению
 * @param string $dest Полный путь куда сохранять измененное изображение
 * @param int $maxwidth Максимальная ширина в px
 * @param int $maxheight Максимальная высота в px
 * @param bool $is_square Создавать квадратное изображение
 * @param int $quality Качество результирующего изображения от 1 до 100
 * @return boolean
 */
function img_resize($src, $dest, $maxwidth, $maxheight = 160, $is_square = false, $quality = 95) {

    if (!file_exists($src)) { return false; }

    $upload_dir = dirname($dest);

    if (!is_writable($upload_dir)) {

        @chmod($upload_dir, 0777);

        if (!is_writable($upload_dir)) {
            return false;
        }

    }

    try {
        $image = new cmsImages($src);
    } catch (Exception $exc) {
        return false;
    }

    if($is_square){

        $image->crop($maxwidth, $maxheight, false, cmsImages::CROPCENTER);

    } else {

        if(!$maxwidth || !$maxheight){

            if(!$maxwidth){
                $image->resizeToHeight($maxheight);
            } else {
                $image->resizeToWidth($maxwidth);
            }

        } else {
            $image->resizeToBestFit($maxwidth, $maxheight);
        }

    }

    $image->save($dest, null, $quality);

    return true;

}

/**
 * Возвращает параметры изображения
 *
 * @param string $path Полный путь к файлу
 * @return boolean|array
 */
function img_get_params($path) {

    if (!is_readable($path)) {
        return false;
    }

    $s = getimagesize($path);
    if ($s === false) {
        return false;
    }

    $exif_data = [];

    $exif = (function_exists('exif_read_data') && $s['mime'] === 'image/jpeg') ? (@exif_read_data($path, null, true)) : null;

    if ($exif) {
        if (isset($exif['COMPUTED']['ApertureFNumber'])) {
            $exif_data['aperturefnumber'] = $exif['COMPUTED']['ApertureFNumber'];
        } elseif (isset($exif['EXIF']['FNumber'])) {
            $num = explode('/', $exif['EXIF']['FNumber']);
            $exif_data['aperturefnumber'] = 'f/' . ($num[0] / $num[1]);
        }
        if (isset($exif['EXIF']['ExposureTime'])) {
            $num = explode('/', $exif['EXIF']['ExposureTime']);
            $exif_data['exposuretime'] = ($num[0] == 1) ? $exif['EXIF']['ExposureTime'] : '1/' . round($num[1] / $num[0]) . 's';
        } elseif (isset($exif['IFD0']['ExposureTime'])) {
            $exif_data['exposuretime'] = $exif['IFD0']['ExposureTime'];
        }
        $make = false;
        if (isset($exif['IFD0']['Make'])) {
            $exif['IFD0']['Make'] = trim($exif['IFD0']['Make']);
            if ($exif['IFD0']['Make'] != 'NIKON CORPORATION' && $exif['IFD0']['Make'] != 'Canon' && $exif['IFD0']['Make'] != 'Lenovo ') {
                $exif_data['camera'] = $exif['IFD0']['Make'];
                $make = true;
            }
        }
        if (isset($exif['IFD0']['Model'])) {
            $exif_data['camera'] = $make ? $exif['IFD0']['Make'] . ' ' . $exif['IFD0']['Model'] : $exif['IFD0']['Model'];
        }

        if (isset($exif['EXIF']['DateTimeOriginal'])) {
            $exif_data['date'] = $exif['EXIF']['DateTimeOriginal'];
        } elseif (isset($exif['EXIF']['DateTimeDigitized'])) {
            $exif_data['date'] = $exif['EXIF']['DateTimeDigitized'];
        }

        if (isset($exif['EXIF']['ISOSpeedRatings'])) {
            $exif_data['isospeedratings'] = $exif['EXIF']['ISOSpeedRatings'];
            if (is_array($exif_data['isospeedratings'])) {
                $exif_data['isospeedratings'] = current($exif_data['isospeedratings']);
            }
        }

        if (isset($exif['EXIF']['FocalLength'])) {
            $num = explode('/', $exif['EXIF']['FocalLength']);
            $exif_data['focallength'] = floor($num[0] / $num[1]) . 'mm';
        }

        if (isset($exif['EXIF']['FocalLengthIn35mmFilm'])) {
            $exif_data['focallengthin35mmfilm'] = $exif['EXIF']['FocalLengthIn35mmFilm'] . 'mm';
        }

        if (isset($exif['IFD0']['Orientation'])) {
            $exif_data['orientation'] = $exif['IFD0']['Orientation'];
        }
    }

    $orientation = 'square';
    if ($s[0] > $s[1]) {
        $orientation = 'landscape';
    }
    if ($s[0] < $s[1]) {
        $orientation = 'portrait';
    }

    return [
        'orientation' => $orientation,
        'width'       => $s[0],
        'height'      => $s[1],
        'mime'        => $s['mime'],
        'exif'        => $exif_data,
        'filesize'    => round(filesize($path))
    ];
}

/**
 * Выполняет команду в shell и возвращает массив строк ответа
 *
 * @param string $command Команда
 * @param string $postfix Строка после команды
 * @return ?array
 */
function console_exec_command($command, $postfix = ' 2>&1') {

    if (!function_exists('exec')) {
        return null;
    }

    $buffer = [];
    $err    = '';

    $result = exec($command . $postfix, $buffer, $err);

    if ($err !== 127) {
        if (!isset($buffer[0])) {
            $buffer[0] = $result;
        }
        $b = strtolower($buffer[0]);
        if (strstr($b, 'error') || strstr($b, ' no ') || strstr($b, 'not found') || strstr($b, 'No such file or directory')) {
            return [];
        }
    } else {
        return [];
    }

    return $buffer;
}
