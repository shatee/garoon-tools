<?php
/**
 * Created by PhpStorm.
 * User: shate
 * Date: 2014/09/05
 * Time: 2:48
 */
require __DIR__ . '/../autoloader.php';

$api = new CybozuGaroonAPI();
$api->setUser(GaroonTools\Conf\Garoon::LOGIN_USER_NAME, GaroonTools\Conf\Garoon::LOGIN_PASSWORD);

$eventModel = new \GaroonTools\Model\Event();
$event = $eventModel->getEventById($argv[1]);
//$event = $api->ScheduleGetEventsById(841247);
var_dump($event);