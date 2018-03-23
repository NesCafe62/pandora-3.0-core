<?php
namespace pandora3\core\Proxy;

class Proxy {

	/**
	 * @var object $instance
	 */
	private $instance;

	/**
	 * @param object $instance
	 */
	public function __construct($instance) {
		$this->instance = $instance;
	}

	/**
	 * @param string $name
	 * @param array $arguments
	 * @return mixed
	 */
	public function __call(string $name, array $arguments) {
		return $this->instance->proxy($name, $arguments);
	}

}