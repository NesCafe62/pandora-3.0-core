<?php
namespace pandora3\core\Renderable;

use pandora3\core\Dynamic\TDynamicProps;

/**
 * Trait TRenderable
 * @package pandora3\core\Renderable
 * @property string $path
 */

trait TRenderable {

	use TDynamicProps;

	/**
	 * @var string $_path
	 */
	private $_path;

	/**
	 * @return string
	 */
	public function getPath() {
		if ($this->_path === null) {
			$class = new \ReflectionClass(get_called_class());
			$this->_path = unixPath(dirname($class->getFileName()));
		}

		return $this->_path;
	}

	/**
	 * @return string
	 */
	public function getViewPath() {
		return $this->path.'/views';
	}

	public function render($view, $params = []) {
		// ;
	}

}