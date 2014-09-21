<?php
/**
 * Created by PhpStorm.
 * User: shate
 * Date: 2014/09/17
 * Time: 2:16
 */
namespace GaroonTools\App;

class JsonView {

	private $data = [];

	public function __construct() {
	}

	public function set($key, $value) {
		if (is_object($value)) {
			$this->data[$key] = get_object_vars($value);
		} else {
			$this->data[$key] = $value;
		}
	}

	public function display() {
		header('Content-Type: application/json; charset=UTF-8');
		echo json_encode($this->data, JSON_UNESCAPED_UNICODE);
	}
}
