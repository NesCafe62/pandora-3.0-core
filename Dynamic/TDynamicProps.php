<?php
namespace pandora\core3\Dynamic;

trait TDynamicProps {

	/**
	 * @param string $param
	 * @return mixed
	 */
	public function __get(string $param) {
		$getterMethod = 'get'.ucfirst($param);
		if (method_exists($this, $getterMethod)) {
			return $this->$getterMethod();
		}
		return parent::__get($param);
	}

}
