<?php
/**
 * Created by PhpStorm.
 * User: shate
 * Date: 2014/08/28
 * Time: 12:30
 */

define('BASE_NAMESPACE', 'GaroonTools');
spl_autoload_register(function($className) {
	$className = ltrim($className, '\\');
	if (strpos($className, BASE_NAMESPACE . '\\') !== 0) {
		return;
	}


	$filePath = __DIR__ . '/' . strtr(substr($className, strlen(BASE_NAMESPACE) + 1), ['\\' => DIRECTORY_SEPARATOR]) . '.php';

	if (file_exists($filePath)) {
		require $filePath;
	}
});

require __DIR__ . '/vendor/Slim/Slim.php';
Slim\Slim::registerAutoloader();
require __DIR__ . '/vendor/Predis/Autoloader.php';
Predis\Autoloader::register();

require __DIR__ . '/vendor/CybozuGaroon3API_lib_0_2/CybozuGaroonAPI.php';
require __DIR__ . '/vendor/Smarty/Smarty.class.php';
