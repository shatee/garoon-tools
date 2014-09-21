<?php
/**
 * Created by PhpStorm.
 * User: shate
 * Date: 2014/09/15
 * Time: 12:05
 */
namespace GaroonTools\Model;

use GaroonTools\Entity\Notification as NotificationEntity;

/**
 * Class Notification
 * @package GaroonTools\Model
 */
class Notification extends Base {

	const CACHE_EXPIRE = 3600; // 1 hour

	/**
	 * @param int $start
	 * @param int|null $end
	 * @return array
	 */
	public function getNotificationsByModifiedSpan($start, $end = null) {
		$notifications = [];

//		echo "<p>NotificationGetNotificationVersions</p>\n";
		$versions = $this->api->NotificationGetNotificationVersions(null, $start, $end);

		foreach ($versions as $version) {
			$moduleId = $version->notification_id->module_id;
			$itemId = (int)$version->notification_id->item;
			$version = (int)$version->version;
			$notifications[] = $this->getNotificationById($moduleId, $itemId, $version);
		}

		return $notifications;
	}

	/**
	 * @param $start
	 * @param null $end
	 * @return array
	 */
	public function getNotificationsByModifiedSpanUnread($start, $end = null) {
		$notifications = $this->getNotificationsByModifiedSpan($start, $end);
		return array_filter($notifications, function($notification) {
			return $notification->readTime === null;
		});
	}

	/**
	 * @param string $moduleId
	 * @param int $itemId
	 * @param int $version
	 * @param bool $enableCache
	 * @return \GaroonTools\Entity\Notification
	 */
	public function getNotificationById($moduleId, $itemId, $version, $enableCache = true) {
		if (!$enableCache) {
			echo "<p>NotificationGetNotificationsById a</p>\n";
			return self::convertApiResponseToEntity(
				$this->api->NotificationGetNotificationsById(new \CbgrnNotificationIdType($moduleId, $itemId)),
				$version
			);
		}

		/** @var NotificationEntity $notification */
		$notification = null;
		if ($version !== null) {
			$res = $this->predis->get(self::makeKey($moduleId, $itemId));
			if (is_string($res)) {
				$notification = unserialize($res);
			}
		}

		// cache がなければ
		if ($notification === null || $notification->versionVersion !== $version) {
//			echo "<p>NotificationGetNotificationsById b</p>\n";
			$notification = self::convertApiResponseToEntity(
				$this->api->NotificationGetNotificationsById(new \CbgrnNotificationIdType($moduleId, $itemId)),
				$version
			);
			$this->predis->setex(self::makeKey($moduleId, $itemId), self::CACHE_EXPIRE, serialize($notification));
		}

		return $notification;
	}

	/**
	 * @param $moduleId
	 * @param $itemId
	 */
	public function updateNotificationConfirm($moduleId, $itemId) {
		$this->api->NotificationConfirmNotification(new \CbgrnNotificationIdType($moduleId, $itemId));
	}

	/**
	 * @param \stdClass $res
	 * @param $version
	 * @return NotificationEntity
	 */
	private static function convertApiResponseToEntity(\stdClass $res, $version) {
		$notification = new NotificationEntity();
		$notification->moduleId = $res->module_id;
		$notification->itemId = (int)$res->item;
		$notification->status = $res->status;
		$notification->isHistory = $res->is_history;
		if (isset($res->read_datetime)) {
			$notification->readTime = strtotime($res->read_datetime);
		}
		$notification->receiveTime = strtotime($res->receive_datetime);
		$notification->subject = $res->subject;
		$notification->subjectUrl = $res->subject_url;
		$notification->abstract = $res->abstract;
		$notification->abstractUrl = $res->abstract_url;
		$notification->senderName = $res->sender_name;
		$notification->senderUserId = (int)$res->sender_id;
		$notification->attached = $res->attached;
		$notification->version = (int)$res->version;
		$notification->versionVersion = $version;
		return $notification;
	}

	private static function makeKey($moduleId, $itemId) {
		return "n_{$moduleId}_{$itemId}";
	}

}
