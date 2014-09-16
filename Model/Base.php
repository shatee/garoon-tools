<?php
/**
 * Created by PhpStorm.
 * User: shate
 * Date: 2014/08/28
 * Time: 12:28
 */

namespace GaroonTools\Model;

/**
 * Class Base
 * @package Model
 */
abstract class Base {
	protected $predis, $api;

	public function __construct() {
		$this->predis = new \Predis\Client(\GaroonTools\Conf\Redis::get_conf());
		$this->api = new \CybozuGaroonAPI(\GaroonTools\Conf\Garoon::LOGIN_USER_NAME, \GaroonTools\Conf\Garoon::LOGIN_PASSWORD);
	}
}