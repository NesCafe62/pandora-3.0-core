<?php
namespace pandora3\core\Router;

use pandora3\core\Http\{IRequest, IResponse};

interface IRouter {
	
	/**
	 * @param string $uri
	 * @param IRequest $request
	 * @param IResponse $response
	 */
	public function dispatch(string $uri, IRequest $request, IResponse $response);

}