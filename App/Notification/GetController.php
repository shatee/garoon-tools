<?php
/**
 * Created by PhpStorm.
 * User: shate
 * Date: 2014/09/15
 * Time: 12:02
 */

namespace GaroonTools\App\Notification;

use GaroonTools\App\View;
use GaroonTools\Model\Event;
use GaroonTools\Model\Notification;

class GetController {

	private static $_SPAN_START_BEFORE_SEC = 1209600; // 2 week

	private $notificationModel;
	private $eventModel;
	private $view;

	public function __construct(View $view) {
		$this->notificationModel = new Notification();
		$this->eventModel = new Event();
		$this->view = $view;
		$this->view->template = 'App/Notification/Notification.tpl';
	}

	public function execute() {
		$notifications = $this->notificationModel->getNotificationsByModifiedSpanUnread(time() - self::$_SPAN_START_BEFORE_SEC);
		$events = [];
		foreach ($notifications as $notification) {
			/** @var \GaroonTools\Entity\Notification $notification */
			if ($notification->moduleId == \GaroonTools\Entity\Notification::MODULE_ID_GRN_SCHEDULE) {
				$events[$notification->itemId] = $this->eventModel->getEventById($notification->itemId, $notification->version);
			}
		}

		$this->view->set('events', $events);
		$this->view->set('categorizedNotifications', $this->categorizeNotifications($notifications, $events));
	}

	/**
	 * @param \GaroonTools\Entity\Notification[] $notifications
	 * @param \GaroonTools\Entity\Event[] $events
	 * @return array
	 */
	private function categorizeNotifications($notifications, $events) {
		$categorizedNotifications = [
			'private' => [],
			'rest' => [],
			'deploySchedule' => [],
			'other' => [],
		];
		foreach ($notifications as $notification) {
			if (
				$notification->moduleId == \GaroonTools\Entity\Notification::MODULE_ID_GRN_SCHEDULE
				&& $events[$notification->itemId]->publicType === \GaroonTools\Entity\Event::PUBLIC_TYPE_PRIVATE
			) {
				$categorizedNotifications['private'][] = $notification;
			} elseif (strpos($notification->subject, '休み') !== false) {
				$categorizedNotifications['rest'][] = $notification;
			} elseif (strpos($notification->subject, '【投入予定】') !== false) {
					$categorizedNotifications['deploySchedule'][] = $notification;
			} else {
				$categorizedNotifications['other'][] = $notification;
			}
		}
		return $categorizedNotifications;
	}

}
