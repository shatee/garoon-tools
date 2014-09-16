<?php
/**
 * Created by PhpStorm.
 * User: shate
 * Date: 2014/08/28
 * Time: 12:23
 */

namespace GaroonTools\Model;

/**
 * Class User
 * @package Model
 */
class User extends Base {

	/**
	 * @param \GaroonTools\Entity\User $user
	 */
	public function set(\GaroonTools\Entity\User $user) {
		$this->predis->set(self::makeKey($user->id), serialize($user));
	}

	/**
	 * @param int $id
	 * @return \GaroonTools\Entity\User|null
	 */
	public function getById($id) {
		$res = $this->predis->get(self::makeKey($id));
		if (is_string($res)) {
			return unserialize($res);
		}
		return null;
	}

	private static function makeKey($id) {
		return 'user_' . $id;
	}
}
