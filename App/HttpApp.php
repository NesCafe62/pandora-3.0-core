<?php
namespace pandora\core3\App;

use pandora\core3\Database\IDatabaseConnection;
use pandora\core3\Http\IRequest;
use pandora\core3\Http\IResponse;
use pandora\core3\Router\IRouter;

/**
 * @property IRequest $request
 * @property IResponse $response
 * @property IRouter $router
 * @property IDatabaseConnection $db
 *
 * @property string $uri
 */

class HttpApp extends BaseApp {

	public function init() {
		$this->di->setDependencies([
			'response' => ['pandora\core3\libs\Http\Response'],
			'request' => ['pandora\core3\libs\Http\Request'],
			'router' => ['pandora\core3\libs\Router\Router'],
			'logger' => ['pandora\core3\libs\Logger\Logger']
		]);

		if (!empty($this->config['db'])) {
			$this->di->set('db', ['pandora\core3\libs\Database\DatabaseConnection', $this->config['db']]);
		}
	}

	/**
	 * Application uri
	 * @var string $uri
	 */
	protected $uri;

	/**
	 * Application uri getter
	 * @return string
	 */
	protected function getUri(): string {
		return $this->uri;
	}

	protected function test() {
	}

	public function handle() {
		$this->uri = '/'.$this->request->get('ENV_URI_PATH');
		$this->router->dispatch($this->request, $this->response);
		$this->test();
	}

	public function run() {
		$this->initParams();
		$this->init();
		$this->handle();
		$this->response->send();
	}

}