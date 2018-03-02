<?php
namespace pandora3\core\Renderable;

interface IRenderable {

	/**
	 * @param $view
	 * @param array $params
	 * @return string
	 */
	public function render(string $view, array $params = []): string;

}