<?php
/**
 * Created by PhpStorm.
 * User: shate
 * Date: 2014/08/28
 * Time: 12:30
 */

spl_autoload_register(function($class) {
	require __DIR__ . '/' . strtr($class, ['\\' => '/']) . '.php';
});

require __DIR__ . '/CybozuGaroon3API_lib_0_2/CybozuGaroonAPI.php';
require __DIR__ . '/Predis/Autoloader.php';
Predis\Autoloader::register();
