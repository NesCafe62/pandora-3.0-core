<?php
namespace pandora3\core\DI;

use \Exception;
use pandora3\core\Debug\CoreException;
use pandora3\core\Debug\Debug;

class DIContainer {

	/**
	 * DIContainer constructor
	 */
	public function __construct() {
		$this->di = new DI();
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
		} catch (Exception $ex) {
			Debug::logException(new CoreException(['DI_CONTAINER_GET_DEPENDENCY_ERROR', static::class], $ex->getCode(), $ex));
			return null;
		}
	}

	/**
	 * @param string $param
	 * @return bool
	 */
	public function __isset(string $param): bool {
		try {
			$value = $this->di->get($param);
			return $value ? true : false;
		} catch (Exception $ex) {
			Debug::logException(new CoreException(['DI_CONTAINER_GET_DEPENDENCY_ERROR', static::class], $ex->getCode(), $ex));
			return false;
		}
	}

}
