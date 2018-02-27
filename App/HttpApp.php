<?php
namespace pandora3\core\App;

use pandora3\core\Debug\CoreException;
use pandora3\core\Debug\Debug;
use pandora3\core\Storage\Database\IDatabaseConnection;
use pandora3\core\Http\{IRequest, IResponse};
use pandora3\core\Router\IRouter;
use \Exception;

/**
 * @property IRequest $request
 * @property IResponse $response
 * @property IRouter $router
 * @property IDatabaseConnection $db
 *
 * @property array $routes
 * @property string $uri
 */

abstract class HttpApp extends BaseApp {

	/**
	 * Gets the application routes
	 * @return array
	 */
	protected function getRoutes(): array {
		return include($this->path.'/routes.php');
	}

	/**
	 * @return array
	 */
	final protected function _getRoutes(): array {
		try {
			return $this->getRoutes();
		} catch (Exception $ex) {
			// 'Application get routes failed'
			Debug::logException(new CoreException('HTTP_APP_GET_ROUTES_FAILED', E_ERROR, $ex));
			return [];
		}
	}

	protected function init(): void {
		$this->di->setDependencies([
			'response' => ['pandora3\libs\Http\Response'],
			'request' => ['pandora3\libs\Http\Request'],
			'router' => function() {
				return new \pandora3\libs\Router\Router($this->routes);
			}
		]);

		if (!empty($this->config['db'])) {
			$this->di->set('db', ['pandora3\libs\Database\DatabaseConnection', $this->config['db']]);
		}
	}

	/**
	 * Application uri.
	 * @var string $uri
	 */
	protected $uri;

	/**
	 * Gets the application uri
	 * @return string
	 */
	protected function getUri(): string {
		return $this->uri;
	}

	protected function test() { // temporary
	}

	protected function handle(): void {
		$this->uri = '/'.$this->request->get('ENV_URI_PATH');
		$this->router->dispatch($this->uri, $this->request, $this->response);
		$this->test(); // temporary
	}

	public function run(): void {
		$this->initParams();
		$this->init();
		$this->handle();
		$this->response->send();
	}

}