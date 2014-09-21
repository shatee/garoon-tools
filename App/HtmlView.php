<?php
/**
 * Created by PhpStorm.
 * User: shate
 * Date: 2014/09/15
 * Time: 14:54
 */
namespace GaroonTools\App;

class HtmlView {

	private $smarty;
	public $template;

	public function __construct() {
		$this->smarty = new \Smarty();
		$this->smarty->setCacheDir(__DIR__ . '/../tmp/smarty_template_cache');
		$this->smarty->setCompileDir(__DIR__ . '/../tmp/smarty_template_compile');
		$this->smarty->setTemplateDir(__DIR__ . '/../templates');
	}

	public function set($key, $value) {
		$this->smarty->assign($key, $value);
	}

	public function display() {
		$this->smarty->display($this->template);
	}
}
