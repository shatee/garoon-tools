<?php
/**
 * Created by PhpStorm.
 * User: tatsuya_akashi
 * Date: 2014/10/03
 * Time: 13:12
 */
require_once __DIR__ . '/../autoloader.php';

echo strftime('%F %T', time()) . ' start' . "\n";
echo strftime('%F %T', time()) . ' start get notifications' . "\n";
$notifications = (new \GaroonTools\Model\Notification())->getNotificationsByModifiedSpanUnread(time() - \GaroonTools\App\Notification\GetController::SPAN_START_BEFORE_SEC);
echo strftime('%F %T', time()) . ' end get notifications' . "\n";

$eventModel = new \GaroonTools\Model\Event();
foreach ($notifications as $notification) {
	if ($notification->moduleId == \GaroonTools\Entity\Notification::MODULE_ID_GRN_SCHEDULE) {
		echo strftime('%F %T', time()) . " start get event ($notification->itemId)\n";
		$eventModel->getEventById($notification->itemId, $notification->version);
		echo microtime(true) . "\n";
		echo strftime('%F %T', time()) . " end get event ($notification->itemId)\n";
	}
}
echo strftime('%F %T', time()) . ' end' . "\n";
