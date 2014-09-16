<?php
/**
 * Created by PhpStorm.
 * User: shate
 * Date: 2014/09/05
 * Time: 3:24
 */
namespace GaroonTools\Entity;

class Follow {
	/** @var int */
	public $id;
	/** @var int */
	public $version;
	/** @var string */
	public $text;
	/** @var int */
	public $creatorUserId;
	/** @var string */
	public $creatorUserName;
	/** @var int */
	public $date;
}
