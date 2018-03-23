<?php
namespace pandora3\core\Dynamic;

use pandora3\core\Debug\CoreException;
use Throwable;

trait TDynamicProps {

	/**
	 * @param string $param
	 * @return mixed
	 * @throws CoreException
	 */
	public function __get(string $param) {
		$getter = 'get'.ucfirst($param);
		if (method_exists($this, $getter)) {
			return $this->$getter();
		}
		try {
			return parent::__get($param);
		} catch (Throwable $ex) {
			throw new CoreException(['PROP_GET_ERROR', static::class, $param], E_ERROR, $ex);
		}
	}

	/**
	 * @param string $param
	 * @return bool
	 * @throws CoreException
	 */
	public function __isset(string $param): bool {
		$getter = 'get'.ucfirst($param);
		if (method_exists($this, $getter)) {
			return true;
		}
		try {
			return parent::__isset($param);
		} catch (Throwable $ex) {
			throw new CoreException(['PROP_ISSET_ERROR', static::class, $param], E_ERROR, $ex);
		}
	}

}