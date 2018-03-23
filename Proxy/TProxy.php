<?php
namespace pandora3\core\Proxy;

use pandora3\core\Debug\CoreException;

trait TProxy {

	/**
	 * @param string $method
	 * @param array $arguments
	 * @return mixed
	 * @throws CoreException
	 */
	public function proxy(string $method, array $arguments) {
		if (!method_exists($this, $method)) {
			throw new CoreException(['PROXY_METHOD_NOT_FOUND', self::class, $method], E_WARNING);
		}
		return function() use ($method, $arguments) {
			return $this->$method(...$arguments);
		};
	}

}