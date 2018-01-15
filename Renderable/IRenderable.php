<?php
namespace pandora\core3\Renderable;

interface IRenderable {

	/**
	 * @param $view
	 * @param array $params
	 * @return string
	 */
	public function render($view, $params = []);

}