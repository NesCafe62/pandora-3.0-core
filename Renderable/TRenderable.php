<?php
namespace pandora3\core\Renderable;

/**
 * @property string $viewPath
 */
trait TRenderable {

	/**
	 * @return string
	 */
	protected function getViewPath(): string {
		return $this->path.'/views';
	}

	/**
	 * @param string $view
	 * @param array $params
	 * @return string
	 */
	public function render(string $view, array $params = []): string {
		return '';
	}

}