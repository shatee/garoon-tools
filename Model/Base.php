<?php
/**
 * Created by PhpStorm.
 * User: shate
 * Date: 2014/08/28
 * Time: 12:28
 */

namespace Model;

/**
 * Class Base
 * @package Model
 */
abstract class Base {
	protected $predis;

	public function __construct() {
		$this->predis = new \Predis\Client(\Conf\Redis::get_conf());
	}
}