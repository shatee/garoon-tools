<?php
/**
 * Created by PhpStorm.
 * User: shate
 * Date: 2014/08/30
 * Time: 1:08
 */
namespace GaroonTools\Entity;

/**
 * Class FacilityGroup
 * @package Entity
 */
class FacilityGroup {
	/** @var int */
	public $id;
	/** @var string */
	public $name;
	/** @var int[] */
	public $facilityIds;
}
