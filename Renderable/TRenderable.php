<?php
namespace pandora3\core\Renderable;

use pandora3\core\Debug\{Debug, CoreException};

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
		try {
			extract($params, EXTR_SKIP);
			ob_start();
			include $this->viewPath.'/'.$view.'.php';
			return ob_get_clean();
		} catch (\Throwable $ex) {
			Debug::logException(new CoreException(['VIEW_RENDER_EXCEPTION', self::class, $view], E_WARNING, $ex));
			return '';
		}
	}

}