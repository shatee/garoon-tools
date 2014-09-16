<?php
/**
 * Created by PhpStorm.
 * User: shate
 * Date: 2014/08/28
 * Time: 12:23
 */

namespace GaroonTools\Model;

/**
 * Class Organization
 * @package Model
 */
class Organization extends Base {

	/**
	 * @param \GaroonTools\Entity\Organization $org
	 */
	public function set(\GaroonTools\Entity\Organization $org) {
		$this->predis->set(self::makeKey($org->id), serialize($org));
	}

	/**
	 * @param int $id
	 * @return \GaroonTools\Entity\Organization|null
	 */
	public function getById($id) {
		$res = $this->predis->get(self::makeKey($id));
		if (is_string($res)) {
			return unserialize($res);
		}
		return null;
	}

	/**
	 * @param int[] $ids
	 */
	public function setTopLevelOrganizationIds($ids) {
		$this->predis->set('top_level_orgs', implode(',', $ids));
	}

	/**
	 * @return int[]|null
	 */
	public function getTopLevelOrganizationIds() {
		$res = $this->predis->get('top_level_orgs');
		if (is_string($res)) {
			return explode(',', $res);
		}
		return null;
	}

	/**
	 * @return \GaroonTools\Entity\Organization[]|null
	 */
	public function getTopLevelOrganizations() {
		$ids = $this->getTopLevelOrganizationIds();
		if ($ids === null) {
			return null;
		}

		$orgs = [];
		foreach ($ids as $id) {
			$orgs[] = $this->getById($id);
		}
		return $orgs;
	}

	private static function makeKey($id) {
		return 'org_' . $id;
	}
}
