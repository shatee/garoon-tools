<?php
/**
 * Created by PhpStorm.
 * User: shate
 * Date: 2014/09/17
 * Time: 2:13
 */
namespace GaroonTools\App\Notification;

use GaroonTools\App\JsonView;
use GaroonTools\Model\Event;
use GaroonTools\Model\Facility;
use GaroonTools\Model\User;

class GetEventController {

	/** @var \GaroonTools\App\JsonView */
	private $view;

	/** @var \GaroonTools\Model\Event */
	private $eventModel;

	/** @var \GaroonTools\Model\Facility */
	private $facilityModel;

	/** @var \GaroonTools\Model\User */
	private $userModel;

	/**
	 * @param JsonView $view
	 */
	public function __construct(JsonView $view) {
		$this->view = $view;
		$this->eventModel = new Event();
		$this->facilityModel = new Facility();
		$this->userModel = new User();
	}

	/**
	 * @param int $id
	 */
	public function execute($id) {
		$event = $this->eventModel->getEventById($id, null, true, true);
		$facility =
			$event->facilityId !== null
			? $facility = $this->facilityModel->getById($event->facilityId)
			: null;
		$members = $this->userModel->getUsersByIds($event->memberUserIds);
		$this->view->set('event', $event);
		$this->view->set('facility', $facility);
		$this->view->set('members', $members);
	}
}
