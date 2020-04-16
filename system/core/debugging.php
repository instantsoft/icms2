<?php

class cmsDebugging {

    const DECIMALS = 5;

    private static $is_enable = false;
    private static $start_time = array();
    private static $points_data = array();

    public static function enable() {

        self::$is_enable = true;

        self::startTimer('cms');

    }

    public static function pointStart($target) {
        self::startTimer($target);
    }

    public static function pointProcess($target, $params, $offset = 2) {

        if(!self::$is_enable){ return false; }

        $backtrace = debug_backtrace();

        while (($backtrace && !isset($backtrace[0]['line']))) {
            array_shift($backtrace);
        }

        if(!isset($backtrace[$offset])){
            $offset -= 1;
        }

        $_offset = $offset + 1;

        $call = $backtrace[$offset];

        if(empty($call['file'])){

            $_offset = $offset;

            $call = $backtrace[$offset-1];

        }

        if (isset($backtrace[$_offset])) {
            if (isset($backtrace[$_offset]['class'])) {
                $call['function'] = $backtrace[$_offset]['class'] . $backtrace[$_offset]['type'] . $backtrace[$_offset]['function'] . '()';
            } else {
                $call['function'] = $backtrace[$_offset]['function'] . '()';
            }
        } else {
            if (isset($backtrace[$offset]['class'])) {
                $call['function'] = $backtrace[$offset]['class'] . $backtrace[$offset]['type'] . $backtrace[$offset]['function'] . '()';
            } elseif(isset($backtrace[$offset]['function'])) {
                $call['function'] = $backtrace[$offset]['function'] . '()';
            } else {
                $call['function'] = '';
            }
        }

        $src = str_replace(cmsConfig::get('root_path'), '/', $call['file']).' => '.$call['line'].($call['function'] ? ' => '.$call['function'] : '');

        self::$points_data[$target][] = array_merge(array(
            'src'  => $src,
            'time' => self::getTime($target)
        ), $params);

        return true;

    }

    public static function getPointsTargets() {

        $_targets = array_keys(self::$points_data);

        $targets = array();

        foreach ($_targets as $target) {
            $targets[$target] = array(
                'title' => string_lang('LANG_DEBUG_TAB_'.$target),
                'count' => count(self::$points_data[$target])
            );
        }

        return $targets;

    }

    public static function loadIncludedFiles() {

        $_files = get_included_files();

        foreach ($_files as $path) {
            self::$points_data['includes'][] = array(
                'src'  => str_replace(cmsConfig::get('root_path'), '/', $path),
                'time' => 0,
                'data' => ''
            );
        }

    }

    public static function getPointsData($target = '') {

        self::loadIncludedFiles();

        if($target && isset(self::$points_data[$target])){
            return self::$points_data[$target];
        }

        return self::$points_data;

    }

    public static function startTimer($target) {
        self::$start_time[$target] = microtime(true);
    }

    public static function getTime($target, $decimals = self::DECIMALS) {
        if(!isset(self::$start_time[$target])){ return 0; }
        return number_format((microtime(true) - self::$start_time[$target]), $decimals);
    }

}
