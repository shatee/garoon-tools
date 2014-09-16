<?php
/**
 * Created by PhpStorm.
 * User: shate
 * Date: 2014/08/30
 * Time: 1:28
 */
namespace GaroonTools\Model;

class Facility extends Base {

	/**
	 * @param \GaroonTools\Entity\Facility $facility
	 * @return mixed
	 */
	public function set(\GaroonTools\Entity\Facility $facility) {
		return $this->predis->set(self::makeKey($facility->id), serialize($facility));
	}

	/**
	 * @param $id
	 * @return \GaroonTools\Entity\Facility|null
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
