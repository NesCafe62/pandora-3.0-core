<?php
namespace pandora3\core\Dynamic;

trait TDynamicPropsInternal {

	/**
	 * @param string $param
	 * @return null|object
	 */
	public function __get(string $param) {
		$getter = 'get'.ucfirst($param);
		$getterInternal = '_'.$getter;
		if (method_exists($this, $getterInternal)) {
			return $this->$getterInternal();
		}
		if (method_exists($this, $getter)) {
			return $this->$getter();
		}
		return parent::__get($param);
	}

	/**
	 * @param string $param
	 * @return bool
	 */
	public function __isset(string $param): bool {
		$getter = 'get'.ucfirst($param);
		$getterInternal = '_'.$getter;
		if (method_exists($this, $getterInternal)) {
			return true;
		}
		if (method_exists($this, $getter)) {
			return true;
		}
		return parent::__isset($param);
	}

}