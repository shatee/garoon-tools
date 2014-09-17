<?php
/**
 * Created by PhpStorm.
 * User: shate
 * Date: 2014/09/15
 * Time: 11:21
 */

$requestFilePath = __DIR__ . $_SERVER['REQUEST_URI'];
if (file_exists($requestFilePath)) {
	$ext = pathinfo($requestFilePath, PATHINFO_EXTENSION);
	if (in_array($ext, [
		'css', 'map',
		'js',
		'jpg', 'gif', 'png', 'html'
	])) {
		echo file_get_contents($requestFilePath);
		exit;
	}
}

require __DIR__ . '/../autoloader.php';

$app = new \Slim\Slim();
$app->config('debug', true);

$app->get('/notification', function() {
	$view = new GaroonTools\App\View();
	(new \GaroonTools\App\Notification\GetController($view))->execute();
	$view->display();
});

$app->get('/deploy_schedule/current', function() {
	(new \GaroonTools\App\DeploySchedule\GetCurrentController())->execute();
});

$app->run();
