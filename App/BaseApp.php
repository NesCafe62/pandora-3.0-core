<?php
namespace pandora3\core\App;

use pandora3\core\DI\DIContainer;
use pandora3\core\Dynamic\TDynamicPropsInternal;
use pandora3\core\Debug\CoreException;
use pandora3\core\Debug\Debug;
use \Exception;

/**
 * @package pandora3\core\App
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
	 * Path to global entry point
	 * @return string
	 */
	protected function getEntryPath(): string {
		return $this->_entryPath;
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
	 * @var string $_entryPath
	 */
	private $_entryPath;

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
	 * Initialises application parameters
	 */
	protected function initParams(): void {
		require(__DIR__.'/../functions.php');
		
		$this->_entryPath = unixPath(getcwd());
	}

	protected abstract function init(): void;

	public abstract function run(): void;

}