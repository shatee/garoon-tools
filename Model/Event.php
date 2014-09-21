<?php
/**
 * Created by PhpStorm.
 * User: shate
 * Date: 2014/09/05
 * Time: 2:56
 */
namespace GaroonTools\Model;

use GaroonTools\Entity\Follow;

class Event extends Base {

	const EXPIRE = 86400; // 1 day
	const EXPIRE_NO_VERSION = 3600; // 1 hour

	/**
	 * @param int $id
	 * @param int|null $version
	 * @param bool $enable_cache
	 * @param bool $needRes
	 * @return \GaroonTools\Entity\Event
	 */
	public function getEventById($id, $version = null, $enable_cache = true, $needRes = false) {
		if (!$enable_cache) {
			return self::convertApiResponseToEntity($this->api->ScheduleGetEventsById($id), $needRes);
		}

		$res = $this->predis->get(self::makeKey($id));
		if (is_string($res)) {
			$event = unserialize($res);
		} else {
			$event = self::convertApiResponseToEntity($this->api->ScheduleGetEventsById($id), $needRes);
		}

		if ($version === null || $event->version !== $version) {
			$event = self::convertApiResponseToEntity($this->api->ScheduleGetEventsById($id), $needRes);
		}

		$this->predis->setex(self::makeKey($id), self::EXPIRE, serialize($event));

		return $event;
	}

	/**
	 * @param \CbgrnEventType $res
	 * @param bool $needRes
	 * @return \GaroonTools\Entity\Event
	 */
	private static function convertApiResponseToEntity(\CbgrnEventType $res, $needRes) {
		$event = new \GaroonTools\Entity\Event();
		$event->id = (int)$res->id;
		$event->type = $res->event_type;
		$event->version = (int)$res->version;
		$event->publicType = $res->public_type;
		$event->plan = $res->plan;
		$event->title = $res->detail;
		$event->description = $res->description;
		$event->allDay = $res->allday;
		$event->memberUserIds = [];
		foreach ($res->member as $member) {
			switch ($member->type) {
				case 'user':
					$event->memberUserIds[] = (int)$member->id;
					break;
				case 'facility':
					$event->facilityId = (int)$member->id;
					break;
			}
		}
		if (isset($res->when->datetime) && $res->when->datetime instanceof \CbgrnEventDateTimeType) {
			$event->dateTimeStart = $res->when->datetime->start;
			$event->dateTimeEnd = $res->when->datetime->end;
		}
		if (isset($res->when->date) && $res->when->date instanceof \CbgrnEventDateType) {
			$event->dateStart = $res->when->date->start;
			$event->dateEnd = $res->when->date->end;
		}
		if (isset($res->repeat_info)) {
			// やっつけ
			$event->repeatInfo = $res->repeat_info;
		}
		if ($needRes) {
			$event->follows = array_map(function ($a) {
				$follow = new Follow();
				$follow->id = (int)$a->id;
				$follow->version = (int)$a->version;
				$follow->text = $a->text;
				$follow->creatorUserId = $a->creator->user_id;
				$follow->creatorUserName = $a->creator->name;
				$follow->date = $a->creator->date;
				return $follow;
			}, $res->follow);
		}
		return $event;
	}
	
	private static function makeKey($id) {
		return "e_{$id}";
	}
}
