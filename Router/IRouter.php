<?php
namespace pandora\core3\Router;

use pandora\core3\Http\{IRequest, IResponse};

interface IRouter {
	
	/**
	 * @param string $uri
	 * @param IRequest $request
	 * @param IResponse $response
	 */
	public function dispatch(string $uri, IRequest $request, IResponse $response);

}