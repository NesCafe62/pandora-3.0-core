<?php
namespace pandora\core3\DI;

use \Exception;
use pandora\core3\DI\DI;

class DIContainer {

	/**
	 * DIContainer constructor
	 */
	public function __construct() {
		$this->di = new DI();
		$this->di->bind($this);
	}

	/**
	 * Dependency-injection container
	 * @var DI $di
	 */
	public $di;

	/**
	 * @param string $param
	 * @return null|object
	 */
	public function __get(string $param) {
		try {
			return $this->di->get($param);
		} catch (Exception $e) {
			// todo: refactor in accordance with debug api
			trigger_error($e->getMessage(), E_USER_WARNING);
			return null;
		}
	}

}
