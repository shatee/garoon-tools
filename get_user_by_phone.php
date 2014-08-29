<?php
/**
 * Created by PhpStorm.
 * User: shate
 * Date: 2014/08/17
 * Time: 23:09
 */
require __DIR__ . '/autoloader.php';

define('REQUEST_USERS_COUNT_AT_ONCE', 100);
define('SLEEP', 0.1);

$api = new CybozuGaroonAPI();
$api->setUser(Conf\Garoon::LOGIN_USER_NAME, Conf\Garoon::LOGIN_PASSWORD);

$phoneParameter = isset($argv[1]) ? $argv[1] : null;
if (!isset($argv[1]) || !ctype_digit(regularizePhoneNumber($argv[1]))) {
	echo "Usage: php get_user_by_phone.php PhoneNumber\n";
	return 1;
}
$phoneParameter = regularizePhoneNumber($argv[1]);

try {
	echo 'requesting BaseGetUserVersions';
	$userVersions = $api->BaseGetUserVersions(null);
	echo ".\n";

	$userIds = [];
	foreach ($userVersions as $userVersion) {
		$userIds[] = $userVersion->id;
	}

	$i = 0;
	while ($bunchUserIds = array_slice($userIds, $i++ * REQUEST_USERS_COUNT_AT_ONCE, REQUEST_USERS_COUNT_AT_ONCE)) {
		usleep(SLEEP * 1000000);
		echo 'requesting BaseGetUsersById';
		$users = $api->BaseGetUsersById($bunchUserIds);
		echo ".\n";

		$foundFlag = false;
		foreach ($users as $user) {
			if (isset($user->phone)) {
				$phoneString = regularizePhoneNumber($user->phone);
				if (preg_match_all('/[0-9]+/', $phoneString, $matches)) {
					foreach ($matches[0] as $phoneNumber) {
//						if (in_array($phoneNumber, ['070', '080', '090'])) {
//							var_dump($user->phone);
//						}
//						echo $phoneNumber . " = " . $phoneParameter . "\n";
						if ($phoneNumber === $phoneParameter) {
							echo "found!\n";
							var_dump($user);
							$foundFlag = true;
						}
					}
				}
			}
		}
	}

	if (!$foundFlag) {
		echo "not found\n";
	}

} catch (Exception $e) {
	echo "[FAILED]\n";
//	var_dump($api->__getLastRequest());
	var_dump($api->__getLastResponse());
}

function regularizePhoneNumber($phoneNumber) {
	return strtr(mb_convert_kana($phoneNumber, 'a', 'utf8'), ['-' => '', '‐' => '', '－' => '']);
}