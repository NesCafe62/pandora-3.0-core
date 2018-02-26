<?php
namespace pandora3\core\App;

use pandora3\core\DI\DIContainer;
use pandora3\core\Dynamic\TDynamicPropsInternal;
use pandora3\core\Debug\{Debug, CoreException};
use pandora3\core\Logger\Logger;
use \Exception;

/**
 * @property string $entryPath
 * @property string $path
 * @property string $config
 */

abstract class BaseApp extends DIContainer {

	use TDynamicPropsInternal;

	/**
	 * Application instance
	 * @var BaseApp $appInstance
	 */
	private static $appInstance = null;
	
	/**
	 * Gets application configuration settings
	 * @return array
	 */
	protected function getConfig(): array {
		return require($this->path.'/config.php');
	}

	/**
	 * Gets the path of application class
	 * @return string
	 */
	protected function getPath(): string {
		$class = new \ReflectionClass(get_called_class());
		return unixPath(dirname($class->getFileName()));
	}

	/**
	 * Returns the application instance
	 * @return BaseApp
	 */
	public static function getInstance(): BaseApp {
		return self::$appInstance;
	}

	/**
	 * BaseApp constructor
	 */
	public function __construct() {
		parent::__construct();
		if (self::$appInstance === null) {
			self::$appInstance = $this;
		}
	}

	/**
	 * @var array $_config
	 */
	private $_config;

	/**
	 * @var string $_path
	 */
	private $_path;

	/**
	 * @return string
	 */
	final protected function _getPath(): string {
		if ($this->_path === null) {
			$this->_path = $this->getPath();
		}
		return $this->_path;
	}

	/**
	 * @return array
	 */
	final protected function _getConfig(): array {
		if ($this->_config === null) {
			try {
				$this->_config = $this->getConfig();
			} catch (Exception $ex) {
				// 'Application get config failed'
				Debug::logException(new CoreException('APP_GET_CONFIG_FAILED', E_ERROR, $ex));
				$this->_config = [];
			}
		}
		return $this->_config;
	}

	/**
	 * @var array $namespaces
	 */
	private static $namespaces = [];

	/**
	 * @param string $namespace
	 * @param string $path
	 */
	public function addNamespace(string $namespace, string $path): void {
		self::$namespaces[trimLeft(str_replace('\\', '/', $namespace), '/')] = $path;
	}

	/**
	 * @param string $className
	 * @return string
	 */
	private function getAutoloadFilename(string $className): string {
		$className = str_replace('\\', '/', $className);
		foreach (self::$namespaces as $namespace => $path) {
			if (startsWith($className.'/', $namespace.'/')) {
				$className = $path.'/' . trimLeft($className, $namespace.'/');
				break;
			}
		}
		return $className . '.php';
	}

	/**
	 * @param string $className
	 */
	public function autoload($className) {
		$filename = self::getAutoloadFilename($className);
		if (!is_file($filename)) {
			return;
		}

		include $filename;
		if (!class_exists($className, false) && !interface_exists($className, false) && !trait_exists($className, false)) {
			Debug::logException(new CoreException(['AUTOLOAD_CLASS_NOT_FOUND', $className, $filename], E_ERROR));
		}
	}

	/**
	 * Initialises application parameters
	 */
	protected function initParams(): void {
		require(dirname(__DIR__).'/functions.php');

		Debug::init(new Logger());

		$namespace = $this->config['namespace'] ?? '\\app';
		if ($namespace) {
			$this->addNamespace($namespace, $this->path);
		}

		spl_autoload_register([$this, 'autoload']);
	}

	protected abstract function init(): void;

	public abstract function run(): void;

}