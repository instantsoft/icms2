<?php

function step($is_submit){

    $info = check_requirements();

    $result = array(
        'html' => render('step_php', array(
            'info' => $info
        ))
    );

    return $result;

}

function check_requirements(){

    $min_php_version = '5.3.0';
	$vars = array('magic_quotes_gpc' => 0, 'register_globals' => 0);
    $extensions = array('date', 'gd', 'json', 'mbstring', 'mysqli', 'session', 'filter');
    $extensions_extra = array('ftp', 'memcache', 'zip', 'curl');

    sort($extensions);
    sort($extensions_extra);

    $info =  array();

    $info['valid'] = true;

    $info['php'] = array(
        'version' => PHP_VERSION,
        'valid' => (version_compare(PHP_VERSION, $min_php_version) >= 0)
    );

    $info['valid'] = $info['valid'] && $info['php']['valid'];

	foreach($vars as $var=>$req){
		$set = ini_get($var);
		$info['vars'][$var] = array(
			'req' => $req,
			'set' => $set
		);
		$info['valid'] = $info['valid'] && ($req == $set);
	}

    foreach($extensions as $ext){
        $loaded = extension_loaded($ext);
        $info['ext'][$ext] = $loaded;
        $info['valid'] = $info['valid'] && $loaded;
    }

    foreach($extensions_extra as $ext){
        $info['ext_extra'][$ext] = extension_loaded($ext);
    }

    return $info;

}