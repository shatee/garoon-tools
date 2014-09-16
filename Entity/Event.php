<?php
/**
 * Created by PhpStorm.
 * User: shate
 * Date: 2014/09/05
 * Time: 3:12
 */
namespace GaroonTools\Entity;

class Event {
	const TYPE_NORMAL = 'normal';

	const PUBLIC_TYPE_PUBLIC = 'public';
	const PUBLIC_TYPE_PRIVATE = 'private';
	const PUBLIC_TYPE_QUALIFIED = 'qualified';

	const PLAN_MTG_INSIDE = '社内MTG';

	/** @var int */
	public $id;
	/** @var string */
	public $type;
	/** @var int */
	public $version;
	/** @var string */
	public $publicType;
	/** @var string */
	public $plan;
	/** @var string */
	public $title;
	/** @var string */
	public $description;
	/** @var bool */
	public $allDay;
	/** @var int[] */
	public $memberUserIds = [];
	/** @var int|null */
	public $facilityId;
	/** @var int */
	public $dateTimeStart;
	/** @var int */
	public $dateTimeEnd;
	/** @var string */
	public $dateStart;
	/** @var string */
	public $dateEnd;
	/** @var Follow[] */
	public $follows = [];
}
