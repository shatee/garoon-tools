<?php
/**
 * Created by PhpStorm.
 * User: shate
 * Date: 2014/08/28
 * Time: 11:11
 */

require __DIR__ . '/../autoloader.php';

$orgModel = new \GaroonTools\Model\Organization();
$userModel = new \GaroonTools\Model\User();

$getUsers = function ($userIds) use ($userModel) {
	if (empty($userIds)) {
		return null;
	}

	$users = [];
	foreach ($userIds as $id) {
		$user = $userModel->getById($id);
		$users[] = [
			'id' => $user->id,
			'name' => $user->name,
		];
	}
	return $users;
};

$getOrgsRecursive = function ($orgIds) use ($orgModel, &$getOrgsRecursive, &$getUsers) {
	if (empty($orgIds)) {
		return null;
	}

	$orgs = [];
	foreach ($orgIds as $id) {
		$org = $orgModel->getById($id);
		$orgs[] = [
			'id' => $org->id,
			'name' => $org->name,
			'orgs' => $getOrgsRecursive($org->organizationIds),
			'members' => $getUsers($org->memberUserIds)
		];
	}
	return $orgs;
};

$orgs = $getOrgsRecursive($orgModel->getTopLevelOrganizationIds());

function displayOrgsRecursive($orgs, $id = null) {
	if (empty($orgs)) {
		return;
	}

	foreach ($orgs as $org) {
		echo "<li>\n";
		echo "<b><input type=\"checkbox\" name=\"_o{$org['id']}\" class=\"org\"><span class=\"open-close closed\">▶</span>{$org['name']}</b>";
		echo "<ul id=\"org_{$org['id']}\" style=\"display:none;\">\n";
		if (
			!in_array($org['id'], [2, 198, 344, 385, 593])
			&& !empty($org['members'])
		) {
			foreach ($org['members'] as $member) {
				echo "<li class=\"member\"><input type=\"checkbox\" name=\"{$member['id']}\" class=\"member\">{$member['name']}</li>\n";
			}
		}
		if (!empty($org['orgs'])) {
			displayOrgsRecursive($org['orgs'], $org['id']);
		}
		echo "</ul>\n";
		echo "</li>\n";
	}
}

?><html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<script type="application/javascript" src="../js/jquery-2.1.1.min.js"></script>
	<script type="application/javascript" src="select_user.js"></script>
</head>
<body>

<span class="all-open">すべて開く</span>
<span class="all-close">すべて閉じる</span>

<ul>
<?php displayOrgsRecursive($orgs); ?>
</ul>

</body>
</html>