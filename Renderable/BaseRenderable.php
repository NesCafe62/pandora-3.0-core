<?php
namespace pandora3\core\Renderable;

class BaseRenderable implements IRenderable {

	/**
	 * @return string
	 */
	public function getPath() {
		$class = new \ReflectionClass(get_called_class());
		return unixPath(dirname($class->getFileName()));
	}

	/**
	 * @return string
	 */
	public function getViewPath() {
		return $this->getPath().'/views';
	}

	public function render($view, $params = []) {
		// ;
	}

}