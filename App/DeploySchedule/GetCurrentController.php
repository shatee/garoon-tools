<?php
/**
 * Created by PhpStorm.
 * User: tatsuya_akashi
 * Date: 2014/09/17
 * Time: 13:58
 */
namespace GaroonTools\App\DeploySchedule;

class GetCurrentController {

	private $requestHM;

	public function __construct() {
		$this->requestHM = date('h:i', $_SERVER['REQUEST_TIME']);
		$this->requestHM = '16:00';
	}

	public function execute() {
		$api = new \CybozuGaroonAPI(\GaroonTools\Conf\Garoon::LOGIN_USER_NAME, \GaroonTools\Conf\Garoon::LOGIN_PASSWORD);
		$events = $api->ScheduleGetEvents(strtotime(date('Y-m-d 00:00')), strtotime(date('Y-m-d 00:01')));

		foreach ($events as $event) {
			if (isset($event->detail) && strpos($event->detail, '【投入予定】') !== false) {
				$description = array_map('trim', explode("\n", $event->description));
				$this->render($description);
			}
		}
	}

	private function render($description) {
		$status = 'yet';
		while (($line = next($description)) !== false) {
			if ($status === 'yet' && strpos($line, $this->requestHM) === 0) {
				$status = 'hit';
			} elseif ($status === 'hit') {
				if (
					strpos($line, '【完了】') !== false
					|| $line === ''
				) {
					break;
				}
				if (!strpos($line, '[') && ctype_digit(substr($line, 0, 1))) {
					exit;
				}
				echo $line . "\n";
			}
		}
	}
}
