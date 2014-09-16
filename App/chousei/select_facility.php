<?php
/**
 * Created by PhpStorm.
 * User: shate
 * Date: 2014/08/30
 * Time: 1:44
 */

require __DIR__ . '/../autoloader.php';

$facilityGroupModel = new \GaroonTools\Model\FacilityGroup();
$facilityModel = new \GaroonTools\Model\Facility();

$groups = $facilityGroupModel->getAllGroups();

$getAndDisplayFacilities = function($ids) use ($facilityModel) {
	$facilities = array_map(function($id) use ($facilityModel) { return $facilityModel->getById($id); }, $ids);
	foreach ($facilities as $facility) {
		echo "<li><input type=\"checkbox\" name=\"f_{$facility->id}\">{$facility->name}</li>\n";
	}
};


?><html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<script type="application/javascript" src="../js/jquery-2.1.1.min.js"></script>
	<script type="application/javascript" src="select_facility.js"></script>
</head>
<body>

<ul>
	<?php foreach ($groups as $group): ?>
	<li>
		<b><input type="checkbox" name="fg_<?php echo $group->id; ?>"><?php echo $group->name; ?></b>
		<ul>
			<?php $getAndDisplayFacilities($group->facilityIds); ?>
		</ul>
	</li>
	<?php endforeach; ?>
</ul>

</body>
</html>