<?php
/**
 * Created by PhpStorm.
 * User: shate
 * Date: 2014/08/28
 * Time: 11:18
 */

namespace Conf;

class Redis {

	const HOST = 'localhost';
	const PORT = '6379';

	public static function get_conf() {
		return [
			'host' => self::HOST,
			'port' => self::PORT,
		];
	}
}