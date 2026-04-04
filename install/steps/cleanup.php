<?php

function step($is_submit) {

    if ($is_submit) {
        return do_cleanup();
    }

    $data = get_cleanup_list();

    return [
        'html' => render('step_cleanup', [
            'cleanup_items' => $data['items'],
            'total_size'   => $data['total_size']
        ])
    ];
}

function get_cleanup_list() {

    $items = [];
    $total_size = 0;

    $installed = $_SESSION['install']['components'] ?? [];
    $data = include PATH . 'data/components.php';

    $mandatory = $data['mandatory'];

    $all_components = [];
    foreach ($data['categories'] as $cat) {
        $all_components = array_merge($all_components, array_keys($cat['components']));
    }

    $not_installed = array_diff($all_components, $installed);

    $controllers_path = dirname(PATH) . '/system/controllers/';
    if (is_dir($controllers_path)) {
        $dirs = scandir($controllers_path);
        foreach ($dirs as $dir) {
            if ($dir === '.' || $dir === '..') continue;
            if (in_array($dir, $mandatory)) continue;
            if (in_array($dir, $installed)) continue;
            if (in_array($dir, $not_installed)) {
                $path = $controllers_path . $dir;
                $size = get_dir_size($path);
                $items[] = [
                    'type'    => 'component',
                    'name'    => $dir,
                    'path'    => $path,
                    'size'    => $size,
                    'title'   => constant($data['categories'][get_component_category($dir, $data)]['components'][$dir]['title'] ?? 'LANG_' . strtoupper($dir))
                ];
                $total_size += $size;
            }
        }
    }

    return [
        'items'      => $items,
        'total_size' => $total_size
    ];
}

function get_component_category($component, $data) {
    foreach ($data['categories'] as $cat_id => $cat) {
        if (isset($cat['components'][$component])) {
            return $cat_id;
        }
    }
    return null;
}

function get_dir_size($dir) {
    $size = 0;
    if (is_dir($dir)) {
        $files = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::CHILD_FIRST
        );
        foreach ($files as $file) {
            if ($file->isFile()) {
                $size += $file->getSize();
            }
        }
    }
    return $size;
}

function do_cleanup() {

    $remove_components = get_post_array('remove_components');

    $errors = [];
    $removed = [];

    foreach ($remove_components as $item) {
        $path = urldecode($item);
        if (is_dir($path)) {
            if (files_remove_directory($path)) {
                $removed[] = basename($path);
            } else {
                $errors[] = basename($path);
            }
        }
    }

    $message = '';
    if ($removed) {
        $message = LANG_CLEANUP_REMOVED . ': ' . implode(', ', $removed);
    }
    if ($errors) {
        $message .= ($message ? "\n" : '') . LANG_CLEANUP_ERRORS . ': ' . implode(', ', $errors);
    }

    return [
        'error'   => !empty($errors),
        'message' => $message ?: LANG_CLEANUP_COMPLETE
    ];
}

function files_remove_directory($directory, $is_clear = false) {

    if (!is_dir($directory)) {
        return false;
    }

    if (!is_writable($directory)) {
        @chmod($directory, 0755);
    }

    $items = array_diff(scandir($directory), ['.', '..']);

    foreach ($items as $item) {
        $path = $directory . DS . $item;
        if (is_dir($path)) {
            if (!files_remove_directory($path)) {
                return false;
            }
        } else {
            if (!@unlink($path)) {
                return false;
            }
        }
    }

    if (!$is_clear) {
        return @rmdir($directory);
    }

    return true;
}
