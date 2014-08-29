<?php
/**
 * Created by PhpStorm.
 * User: shate
 * Date: 2014/08/30
 * Time: 1:44
 */

require __DIR__ . '/../autoloader.php';

$facilityGroupModel = new \Model\FacilityGroup();
$facilityModel = new \Model\Facility();

$groups = $facilityGroupModel->