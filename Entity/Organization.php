<?php
/**
 * Created by PhpStorm.
 * User: shate
 * Date: 2014/08/28
 * Time: 12:25
 */

namespace GaroonTools\Entity;

/**
 * Class Organization
 * @package Entity
 */
class Organization {
	/** @var int */
	public $id;
	/** @var string */
	public $name;
	/** @var int[] */
	public $memberUserIds = [];
	/** @var int[] */
	public $organizationIds = [];
	/** @var int|null */
	public $parent;
}
