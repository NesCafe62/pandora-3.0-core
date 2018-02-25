<?php
namespace pandora3\core\Renderable;

interface IRenderable {

	/**
	 * @return string
	 */
	public function getViewPath(): string;

	/**
	 * @param $view
	 * @param array $params
	 * @return string
	 */
	public function render($view, $params = []): string;

}