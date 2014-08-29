<?php
/**
 * Created by PhpStorm.
 * User: shate
 * Date: 2014/08/28
 * Time: 11:15
 */
require __DIR__ . '/../autoloader.php';

class BaseMaker {

	/** @var CybozuGaroonAPI */
	private static $api_substance;
	/** @var CybozuGaroonAPI */
	protected $api;

	public function __construct() {
		$this->init();
	}

	private function init() {
		$this->api = $this->get_api();
	}

	protected function get_api() {
		if (!isset(self::$api_substance)) {
			self::$api_substance = new CybozuGaroonAPI();
			self::$api_substance->setUser(Conf\Garoon::LOGIN_USER_NAME, Conf\Garoon::LOGIN_PASSWORD);
		}
		return self::$api_substance;
	}

	protected function output($message) {
		echo strftime('%F %T') . ' ' . $message . "\n";
	}
}

class OrganizationListMaker extends BaseMaker {

	private static $numGetOrganizationsOne = 100;

	/** @var \Model\Organization */
	private $orgModel;

	public function __construct() {
		parent::__construct();
		$this->orgModel = new \Model\Organization();
	}

	public function make() {
		$orgIds = $this->getAllOrganizationIds();
		$orgs = $this->getOrganizationsByIds($orgIds);
		foreach ($orgs as $org) {
			$this->orgModel->set($org);
		}

		// 親組織が存在しない組織=TOPレベル組織一覧を生成
		$topLevelIds = [];
		foreach ($orgs as $org) {
			if ($org->parent === null) {
				$topLevelIds[] = $org->id;
			}
		}
		$this->orgModel->setTopLevelOrganizationIds($topLevelIds);
	}

	/**
	 * @return array int[]
	 */
	private function getAllOrganizationIds() {
		$res = $this->api->BaseGetOrganizationVersions(null);
		$this->output('BaseGetOrganizationVersions');
		return array_map(function($a) { return (int)$a->id; }, $res);
	}

	/**
	 * @param int $id
	 * @return \Entity\Organization[]
	 */
	private function getOrganizationsByIds($ids) {
		$orgs = [];
		while ($divOrgIds = array_splice($ids, 0, self::$numGetOrganizationsOne)) {
			foreach ($this->api->BaseGetOrganizationsById($divOrgIds) as $resOrg) {
				$orgs[(int)$resOrg->key] = $this->formatOrganization($resOrg);
			}
			$this->output('BaseGetOrganizationsById');
		}
		return $orgs;
	}

	/**
	 * @param stdClass $resOrg
	 * @return \Entity\Organization
	 */
	private function formatOrganization($resOrg) {
		$memberUserIds = [];
		if (isset($resOrg->members->user)) {
			if (is_array($resOrg->members->user)) {
				foreach ($resOrg->members->user as $user) {
					$memberUserIds[] = (int)$user->id;
				}
			} else {
				$memberUserIds[] = (int)$resOrg->members->user->id;
			}
		}

		$orgIds = [];
		if (isset($resOrg->organization)) {
			if (is_array($resOrg->organization)) {
				foreach ($resOrg->organization as $subOrg) {
					$orgIds[] = (int)$subOrg->key;
				}
			} else {
				$orgIds[] = (int)$resOrg->organization->key;
			}
		}

		$org = new \Entity\Organization();
		$org->id = (int)$resOrg->key;
		$org->name = (string)$resOrg->name;
		$org->organizationIds = $orgIds;
		$org->memberUserIds = $memberUserIds;
		if (isset($resOrg->parent_organization)) {
			$org->parent = (int)$resOrg->parent_organization;
		}
		return $org;
	}
}

class UserListMaker extends BaseMaker {

	private static $numGetUsersOne = 100;

	private $userModel;

	public function __construct() {
		parent::__construct();
		$this->userModel = new \Model\User();
	}

	public function make() {
		$this->output('start UserListMaker');
		$this->updateAllUsers();
		$this->output('end UserListMaker');
	}

	private function updateAllUsers() {

		$userIds = $this->getAllUserIds();
		$users = $this->getUsersByIds($userIds);
		foreach ($users as $user) {
			$this->userModel->set($user);
		}
		return $users;
	}

	private function getAllUserIds() {
		$userIds = [];
		$res = $this->api->BaseGetUserVersions(null);
		$this->output('BaseGetUserVersions');
		foreach ($res as $resUserVersion) {
			$userIds[] = (int)$resUserVersion->id;
		}
		return $userIds;
	}

	/**
	 * @param int[] $userIds
	 * @return array
	 */
	private function getUsersByIds($userIds) {
		$users = [];
		while ($divUserIds = array_splice($userIds, 0, self::$numGetUsersOne)) {
			foreach ($this->api->BaseGetUsersById($divUserIds) as $user) {
				$users[(int)$user->key] = $this->formatUser($user);
			}
			$this->output('BaseGetUsersById');
		}
		return $users;
	}

	private function formatUser($resUser) {
			$user = new \Entity\User();
			$user->id = (int)$resUser->key;
			$user->name = (string)$resUser->name;
			$user->email = isset($resUser->email) ? (string)$resUser->email : '';
			return $user;
	}
}

(new UserListMaker())->make();
(new OrganizationListMaker())->make();