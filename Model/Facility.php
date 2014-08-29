<?php
/**
 * Created by PhpStorm.
 * User: shate
 * Date: 2014/08/30
 * Time: 1:28
 */
namespace Model;

class Facility extends Base {

	/**
	 * @param \Entity\Facility $facility
	 * @return mixed
	 */
	public function set(\Entity\Facility $facility) {
		return $this->predis->set(self::makeKey($facility->id), serialize($facility));
	}

	/**
	 * @param $id
	 * @return \Entity\Facility|null
	 */
	public function getById($id) {
		$res = $this->predis->get(self::makeKey($id));
		if (is_string($res)) {
			return unserialize($res);
		}
		return null;
	}

	private static function makeKey($id) {
		return 'f_' . $id;
	}
}
