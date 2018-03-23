<?php
namespace pandora3\core\App;

use pandora3\core\DI\DIContainer;
use pandora3\core\Dynamic\TDynamicPropsInternal;
use pandora3\core\Debug\{Debug, CoreException};
use pandora3\core\Plugin\IPlugin;
use pandora3\core\Proxy\Proxy;
use pandora3\core\Proxy\TProxy;
use Exception;

/**
 * @property string $path
 * @property string $pluginsPath
 * @property string $config
 */
abstract class BaseApp extends DIContainer {

	use TDynamicPropsInternal;

	use TProxy;

	/**
	 * Application instance
	 * @var BaseApp $appInstance
	 */
	private static $appInstance = null;

	/**
	 * Autoload namespaces
	 * @var array $namespaces
	 */
	private $namespaces = [];

	/**
	 * @var bool $namespacesNeedUpdate
	 */
	private $namespacesNeedUpdate;

	/**
	 * Plugins namespaces
	 * @var array $pluginsNamespaces
	 */
	private $pluginsNamespaces = [];

	/**
	 * Path aliases
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
	 * @var string $_pluginsPath
	 */
	private $_pluginsPath;

	/**
	 * @var string $mode
	 */
	private $mode;

	/**
	 * @var self $proxy
	 */
	protected $proxy;
	
	/**
	 * Gets application configuration settings
	 * @param string $mode
	 * @return array
	 */
	protected function getConfig(string $mode): array {
		return $this->loadConfig([
			$this->path.'/config/config.php',
			$this->path.'/config/config_'.$mode.'.php',
			$this->path.'/config/local.php'
		]);
	}

	/**
	 * Loads and merges configuration files
	 * @param string|array $configFiles
	 * @return array
	 */
	final protected function loadConfig($configFiles): array {
		if (is_string($configFiles)) {
			$configFiles = [$configFiles];
		}
		$config = [];
		foreach ($configFiles as $configFile) {
			$config = array_replace($config, require $configFile);
		}
		return $config;
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
	 * Gets the path of application plugins
	 * @return string
	 */
	protected function getPluginsPath(): string {
		return $this->path.'/plugins';
	}

	/**
	 * Returns the application instance
	 * @return BaseApp
	 */
	final public static function getInstance(): BaseApp {
		return self::$appInstance;
	}

	public function __construct() {
		parent::__construct();
		$this->proxy = new Proxy($this);
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
	 * @return string
	 */
	final protected function _getPluginsPath(): string {
		if ($this->_pluginsPath === null) {
			$this->_pluginsPath = $this->getPluginsPath();
		}
		return $this->_pluginsPath;
	}

	/**
	 * @return array
	 */
	final protected function _getConfig(): array {
		if ($this->_config === null) {
			try {
				$this->_config = $this->getConfig($this->mode);
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
	final public function setAlias(string $alias, string $path): bool {
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
	final public function getAliasPath(string $path): string {
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
	final public function addNamespace(string $namespace, string $path): bool {
		if (!$namespace) {
			Debug::logException(new CoreException(['APP_ADD_NAMESPACE_IS_EMPTY', $path], E_WARNING));
			return false;
		}

		try {
			$namespace = trimLeft(str_replace('\\', '/', $namespace), '/');
			$this->namespaces[$namespace] = $this->getAliasPath($path);
			$this->namespacesNeedUpdate = true;
			return true;
		} catch (Exception $ex) {
			Debug::logException($ex);
		}
		return false;
	}

	private function updateNamespaces() {
		if ($this->namespacesNeedUpdate) {
			uksort($this->namespaces, function($namespaceFirst, $namespaceSecond) {
				return strlen($namespaceSecond) <=> strlen($namespaceFirst);
			});

			$this->namespacesNeedUpdate = false;
		}
	}

	/**
	 * @param string $className
	 * @return string
	 */
	private function getAutoloadFilename(string $className): string {
		$this->updateNamespaces();
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
	final public function autoload($className): void {
		$filename = self::getAutoloadFilename($className);
		if (!is_file($filename)) {
			return;
		}

		include $filename;

		if (
			!class_exists($className, false) &&
			!interface_exists($className, false) &&
			!trait_exists($className, false)
		) {
			Debug::logException(new CoreException(['AUTOLOAD_CLASS_NOT_FOUND', $className, $filename], E_ERROR));
		}
	}
	
	/**
	 * @param string $pluginName
	 * @return null|IPlugin
	 */
	final protected function loadPlugin(string $pluginName) {
		$className = $pluginName.'Plugin';
		foreach ($this->pluginsNamespaces as $namespace) {
			$classNameFull = $namespace.'\\'.$pluginName.'\\'.$className;
			if (class_exists($classNameFull)) {
				return new $classNameFull();
			}
		}
		
		Debug::logException(new CoreException(['APP_LOAD_PLUGIN_NOT_FOUND', $className], E_WARNING));
		return null;
	}

	/**
	 * @param string $namespace
	 * @param string $path
	 * @return bool
	 */
	final public function addPluginsNamespace(string $namespace, string $path): bool {
		if (!$namespace) {
			Debug::logException(new CoreException(['APP_ADD_PLUGINS_NAMESPACE_IS_EMPTY', $path], E_WARNING));
			return false;
		}

		if (!$this->addNamespace($namespace, $path)) {
			Debug::logException(new CoreException(['APP_ADD_PLUGINS_NAMESPACE_FAILED', $namespace, $path], E_WARNING));
			return false;
		}

		array_unshift($this->pluginsNamespaces, $namespace);
		return true;
	}

	/**
	 * Initialises application parameters
	 * @param string $mode
	 */
	final protected function initParams(string $mode): void {
		$this->mode = $mode;
		$namespace = preg_replace('#\\\\[^\\\\]*$#', '', static::class);

		$this->setAlias('@pandora3', dirname(dirname(__DIR__)));
		$this->setAlias('@app', $this->path);
		$this->setAlias('@plugins', $this->pluginsPath);
		$this->addNamespace($namespace, '@app');
		$this->addPluginsNamespace('pandora3\plugins', '@pandora3/plugins');
		$this->addPluginsNamespace($namespace.'\\plugins', '@plugins');

		// $this->addPluginsNamespace('common\\plugins', 'common.plugins');

		// $this->addNamespace('app\models', '@app/models');

		spl_autoload_register([$this, 'autoload']);
	}

	protected abstract function init(): void;

	public abstract function run(): void;

}