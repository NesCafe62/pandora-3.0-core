<?php
namespace pandora\core3\DI;

// use pandora\core3\Debug\BaseException;
use \Exception;
use \Closure;

class DI {

	/**
	 * DI constructor
	 * @param array $dependencies
	 */
	public function __construct($dependencies = []) {
		if ($dependencies) {
			$this->setDependencies($dependencies);
		}
	}

	/**
	 * DI container instance
	 * @var object $containerInstance
	 */
	private $containerInstance = null;

	/**
	 * Binds the DI container instance
	 * @param object $containerInstance
	 */
	public function bind($containerInstance) {
		$this->containerInstance = $containerInstance;
	}

	/**
	 * Array of dependencies
	 * @var array $dependencies
	 */
	private $dependencies = [];

	/**
	 * Array of instances
	 * @var array $instances
	 */
	private $instances = [];

	/**
	 * @param string $key
	 * @param array|Closure $constructionParams
	 */
	public function set(string $key, $constructionParams) {
		if (array_key_exists($key, $this->dependencies)) {
			// todo: refactor in accordance with debug api
			trigger_error('dependency already set', E_USER_NOTICE);
		}
		$this->dependencies[$key] = $constructionParams;
	}

	/**
	 * @param array $dependencies
	 */
	public function setDependencies(array $dependencies) {
		foreach ($dependencies as $key => $constructionParams) {
			$this->set($key, $constructionParams);
		}
	}

	/**
	 * @param array|Closure $constructionParams
	 * @param array|null $overrideParams
	 * @throws Exception
	 * @return null|object
	 */
	private function createInstance($constructionParams, $overrideParams = null) {
		if ($constructionParams instanceof Closure) {
			$args = $overrideParams ?? [];
			array_unshift($args, $this->containerInstance);
			return call_user_func_array($constructionParams, $args);
		} else {
			$className = array_shift($constructionParams);
			$constructionParams = $overrideParams ?? $constructionParams;
			try {
				if ($constructionParams) {
					return call_user_func_array([$className, '__construct'], $constructionParams);
				} else {
					return new $className();
				}
			} catch (Exception $e) {
				// todo: refactor in accordance with debug api
				// Debug::logException();
				throw new Exception('Creating class "'.$className.'" failed', E_WARNING);
			}
		}
	}

	/**
	 * @param string $key
	 * @param bool $isInstance
	 * @param array|null $overrideParams
	 * @throws Exception
	 * @return null|object
	 */
	public function _getDependency(string $key, bool $isInstance, $overrideParams = null) {
		if (!array_key_exists($key, $this->dependencies)) {
			// todo: refactor in accordance with debug api
			throw new Exception('dependency not exist', E_WARNING);
		}
		$constructionParams = $this->dependencies[$key];
		try {
			if ($isInstance) {
				if (!array_key_exists($key, $this->instances)) {
					$this->instances[$key] = $this->createInstance($constructionParams, $overrideParams);
				}
				return $this->instances[$key];
			} else {
				return $this->createInstance($constructionParams, $overrideParams);
			}
		} catch (\Throwable $e) {
			// todo: refactor in accordance with debug api
			ob_start();
			var_dump($constructionParams);
			$params = '<pre>'.ob_get_clean().'</pre>';
			trigger_error('DI dependency not created "'.$key.'" '.$e->getMessage(), E_USER_WARNING);
			trigger_error($params, E_USER_WARNING);
			// trigger_error($e->getMessage(), E_USER_WARNING);
			return null;
		}
	}

	/**
	 * @param string $key
	 * @param array|null $overrideParams
	 * @throws Exception
	 * @return null|object
	 */
	public function create(string $key, $overrideParams = null) {
		return $this->_getDependency($key, false, $overrideParams);
	}

	/**
	 * @param string $key
	 * @param array|null $overrideParams
	 * @throws Exception
	 * @return null|object
	 */
	public function get(string $key, $overrideParams = null) {
		return $this->_getDependency($key, true, $overrideParams);
	}

}