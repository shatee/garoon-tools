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
$app->get('/notification', function() {
	$view = new GaroonTools\App\HtmlView();
	(new \GaroonTools\App\Notification\GetController($view))->execute();
	$view->display();
});
$app->get('/notification/event/:id', function($id) {
	$view = new GaroonTools\App\JsonView();
	(new \GaroonTools\App\Notification\GetEventController($view))->execute((int)$id);
	$view->display();
});
$app->post('/notification/confirm/:moduleId/:itemId', function($moduleId, $itemId) {
	$view = new GaroonTools\App\JsonView();
	(new \GaroonTools\App\Notification\ConfirmController($view))->execute($moduleId, $itemId);
	$view->display();
});
$app->post('/notification/confirm_multi/', function() {
	$view = new GaroonTools\App\JsonView();
	(new \GaroonTools\App\Notification\ConfirmController($view))->execute(
		array_map(function($item) { return explode(':', $item); }, explode(',', $_POST['items']))
	);
	$view->display();
});

$app->get('/deploy_schedule/current', function() {
	(new \GaroonTools\App\DeploySchedule\GetCurrentController())->execute();
});

$app->run();
