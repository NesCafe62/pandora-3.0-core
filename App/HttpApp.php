<?php
namespace pandora\core3\App;

use pandora\core3\Debug\CoreException;
use pandora\core3\Debug\Debug;
use pandora\core3\Storage\Database\IDatabaseConnection;
use pandora\core3\Http\{IRequest, IResponse};
use pandora\core3\Router\IRouter;
use \Exception;

/**
 * @property IRequest $request
 * @property IResponse $response
 * @property IRouter $router
 * @property IDatabaseConnection $db
 *
 * @property string $uri
 */

abstract class HttpApp extends BaseApp {

	/**
	 * Gets the application routes.
	 * @return array
	 */
	protected function getRoutes() {
		try {
			return include($this->path.'/routes.php');
		} catch (Exception $ex) {
			// 'Application routes not loaded'
			Debug::logException(new CoreException('HTTP_APP_GET_ROUTES_FILE_NOT_LOADED', E_ERROR, $ex));
			return [];
		}
	}

	protected function init() {
		// var_dump($this->config);
		$this->di->setDependencies([
			'response' => ['pandora\core3\libs\Http\Response'],
			'request' => ['pandora\core3\libs\Http\Request'],
			'router' => function($self) { /** @var HttpApp $self */
				return new \pandora\core3\libs\Router\Router($self->getRoutes());
			},
			'logger' => ['pandora\core3\libs\Logger\Logger']
		]);

		if (!empty($this->config['db'])) {
			$this->di->set('db', ['pandora\core3\libs\Database\DatabaseConnection', $this->config['db']]);
		}
	}

	/**
	 * Application uri.
	 * @var string $uri
	 */
	protected $uri;

	/**
	 * Gets the application uri.
	 * @return string
	 */
	protected function getUri(): string {
		return $this->uri;
	}

	protected function test() {
	}

	protected function handle() {
		$this->uri = '/'.$this->request->get('ENV_URI_PATH');
		$this->router->dispatch($this->uri, $this->request, $this->response);
		$this->test();
	}

	public function run() {
		$this->initParams();
		$this->init();
		$this->handle();
		$this->response->send();
	}

}