<?php
/**
 * Created by PhpStorm.
 * User: shate
 * Date: 2014/08/30
 * Time: 1:05
 */
namespace Entity;

/**
 * Class Facility
 * @package Entity
 */
class Facility {
	/** @var int */
	public $id;
	/** @var string */
	public $name;
	/** @var int|null */
	public $facilityGroupId;
}
