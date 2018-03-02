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
	 * @var array $namespaces
	 */
	private $namespaces = [];

	/**
	 * @var array $aliases
	 */
	private $aliases = [];

	/**
	 * @var array $_config
	 */
	private $_config;

	/**
	 * @var string $_path
	 */
	private $_path;
	
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
		$class = new \ReflectionClass(static::class);
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
	 * @param string $alias
	 * @param string $path
	 * @return bool
	 */
	public function setAlias(string $alias, string $path): bool {
		try {
			$this->aliases[$alias] = $this->getAliasPath($path);
			return true;
		} catch (Exception $ex) {
			Debug::logException($ex);
		}
		return false;
	}

	/**
	 * Replaces the alias in path
	 * @param string $path
	 * @return string
	 * @throws CoreException
	 */
	public function getAliasPath(string $path): string {
		if (startsWith($path, '@')) {
			foreach ($this->aliases as $alias => $aliasPath) {
				if (startsWith($path.'/', $alias.'/')) {
					return $aliasPath . trimLeft($path, $alias);
				}
			}
			throw new CoreException(['APP_ALIAS_PATH_UNKNOWN', $path], E_WARNING);
		}
		return $path;
	}

	/**
	 * @param string $namespace
	 * @param string $path
	 * @return bool
	 */
	public function addNamespace(string $namespace, string $path): bool {
		if (!$namespace) {
			Debug::logException(new CoreException(['APP_ADD_NAMESPACE_IS_EMPTY', $path], E_WARNING));
			return false;
		}

		try {
			$this->namespaces[trimLeft(str_replace('\\', '/', $namespace), '/')] = $this->getAliasPath($path);
			return true;
		} catch (Exception $ex) {
			Debug::logException($ex);
		}
		return false;
	}

	/**
	 * @param string $className
	 * @return string
	 */
	private function getAutoloadFilename(string $className): string {
		$className = str_replace('\\', '/', $className);
		foreach ($this->namespaces as $namespace => $path) {
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
	public function autoload($className): void {
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

		$this->setAlias('@app', $this->path);
		$this->setAlias('@plugins', '@app/plugins');
		$this->addNamespace($this->config['namespace'] ?? '\\app', '@app');

		// $this->addNamespace('\\app\\models', '@app/models');
		// $this->addNamespace('\\app\\plugins', '@app/plugins');

		spl_autoload_register([$this, 'autoload']);
	}

	protected abstract function init(): void;

	public abstract function run(): void;

}