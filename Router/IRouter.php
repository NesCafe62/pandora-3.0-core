<?php
namespace pandora\core3\Router;

use pandora\core3\Http\{IRequest, IResponse};

interface IRouter {

	public function dispatch(IRequest $request, IResponse $response);

}