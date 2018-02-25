<?php
namespace pandora3\core\DI;

use \Exception;

class DIContainer {

	/**
	 * DIContainer constructor.
	 */
	public function __construct() {
		$this->di = new DI();
	}

	/**
	 * Dependency-injection container.
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

	/**
	 * @param string $param
	 * @return bool
	 */
	public function __isset(string $param) {
		try {
			$value = $this->di->get($param);
			return $value ? true : false;
		} catch (Exception $e) {
			// todo: refactor in accordance with debug api
			trigger_error($e->getMessage(), E_USER_WARNING);
			return false;
		}
	}

}
