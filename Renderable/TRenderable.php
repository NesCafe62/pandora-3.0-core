<?php
namespace pandora3\core\Renderable;

use pandora3\core\Dynamic\TDynamicProps;

/**
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
	public function getPath(): string {
		if ($this->_path === null) {
			$class = new \ReflectionClass(get_called_class());
			$this->_path = unixPath(dirname($class->getFileName()));
		}

		return $this->_path;
	}

	/**
	 * @return string
	 */
	public function getViewPath(): string {
		return $this->path.'/views';
	}

	public function render(string $view, $params = []): string {
		return '';
	}

}