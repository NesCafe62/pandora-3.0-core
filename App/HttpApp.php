<?php
namespace pandora\core3\App;

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
		} catch (Exception $e) {
			// Debug::logException($e);
			// todo: refactor in accordance with debug api
			trigger_error('Application routes not loaded', E_USER_ERROR);
			return [];
		}
	}

	protected function init() {
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