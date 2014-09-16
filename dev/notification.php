<?php
/**
 * Created by PhpStorm.
 * User: shate
 * Date: 2014/09/05
 * Time: 2:28
 */
require __DIR__ . '/../autoloader.php';

$api = new CybozuGaroonAPI();
$api->setUser(GaroonTools\Conf\Garoon::LOGIN_USER_NAME, GaroonTools\Conf\Garoon::LOGIN_PASSWORD);

$versions = $api->NotificationGetNotificationVersions(null, strtotime('-1 week'));
echo "NotificationGetNotificationVersions\n";
var_dump($versions);

$notifications = [];
foreach ($versions as $version) {
	$notifications[] = $api->NotificationGetNotificationsById(
		new CbgrnNotificationIdType($version->notification_id->module_id, $version->notification_id->item)
	);
	echo "NotificationGetNotificationsById\n";
}

var_dump($notifications);
