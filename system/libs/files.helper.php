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

    $filename = mb_strtolower($filename);
    $filename = preg_replace(array('/[\&]/', '/[\@]/', '/[\#]/'), array('-and-', '-at-', '-number-'), $filename);
    $filename = preg_replace('/[^(\x20-\x7F)]*/','', $filename);
    $filename = str_replace(' ', '-', $filename);
    $filename = str_replace('\'', '', $filename);
    $filename = preg_replace('/[^\w\-\.]+/', '', $filename);
    $filename = preg_replace('/[\-]+/', '-', $filename);

    return $filename;

}

function file_get_contents_from_url($url){

    $data = @file_get_contents($url);

    if ($data===false){

        if (function_exists('curl_init')){

            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_HEADER, false);
            curl_setopt($curl, CURLOPT_TIMEOUT, 10);
            $data = curl_exec($curl);
            curl_close($curl);

        }

    }

    return $data;

}

function file_save_from_url($url, $destination){

    if (!function_exists('curl_init')){ return false; }

    $dest_file = @fopen($destination, "w");

    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_FILE, $dest_file);
    curl_setopt($curl, CURLOPT_HEADER, false);
    curl_exec($curl);
    curl_close($curl);
    fclose($dest_file);

    return true;

}