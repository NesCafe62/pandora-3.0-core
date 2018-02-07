<?php
namespace pandora\core3\Renderable;

use pandora\core3\Dynamic\TDynamicProps;

/**
 * Trait TRenderable
 * @package pandora\core3\Renderable
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