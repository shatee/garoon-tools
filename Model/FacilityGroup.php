<?php
/**
 * Created by PhpStorm.
 * User: shate
 * Date: 2014/08/30
 * Time: 1:14
 */
namespace Model;

class FacilityGroup extends Base {

	/**
	 * @param \Entity\FacilityGroup $group
	 */
	public function set(\Entity\FacilityGroup $group) {
		return $this->predis->set(self::makeKey($group->id), serialize($group));
	}

	/**
	 * @param int[] $ids
	 * @return mixed
	 */
	public function setAllGroupIds(array $ids) {
		return $this->predis->set('fgids', implode(',', $ids));
	}

	/**
	 * @return int[]|null
	 */
	public function getAllGroupIds() {
		$res = $this->predis->get('fgids');
		if (is_string($res)) {
			return array_map('intval', explode(',', $res));
		}
		return null;
	}

	/**
	 * @return \Entity\FacilityGroup[]
	 */
	public function getAllGroups() {
		$groupIds = $this->getAllGroupIds();
		$groups = [];
		foreach ($groupIds as $id) {
			$groups[] = $this->getById($id);
		}
		return $groups;
	}

	/**
	 * @param int $id
	 * @return \Entity\FacilityGroup|null
	 */
	public function getById($id) {
		$res = $this->predis->get(self::makeKey($id));
		if (is_string($res)) {
			return unserialize($res);
		}
		return null;
	}

	public static function makeKey($id) {
		return 'fg_' . $id;
	}
}
