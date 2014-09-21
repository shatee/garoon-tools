<?php
/**
 * Created by PhpStorm.
 * User: shate
 * Date: 2014/09/22
 * Time: 0:23
 */
namespace GaroonTools\App\Notification;

use GaroonTools\App\JsonView;
use GaroonTools\Model\Notification;

class ConfirmController {

	/** @var \GaroonTools\Model\Notification */
	private $notificationModel;

	/** @var \GaroonTools\App\Notification\JsonView */
	private $view;

	public function __construct(JsonView $view) {
		$this->notificationModel = new Notification();
		$this->view = $view;
	}

	public function execute($items) {
		foreach ($items as $item) {
			$this->notificationModel->updateNotificationConfirm($item[0], $item[1]);
		}
		$this->view->set('result', 'success');
	}

}
