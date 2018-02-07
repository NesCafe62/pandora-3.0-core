<?php
namespace pandora\core3\App;

use pandora\core3\Debug\CoreException;
use pandora\core3\Debug\Debug;
use pandora\core3\Dynamic\DIDynamic\DIContainerDynamic;
use \Exception;

/**
 * Class BaseApp
 * @package pandora\core3\App
 * @property string $entryPath
 * @property string $path
 * @property string $config
 */

abstract class BaseApp extends DIContainerDynamic {

	/**
	 * Application instance.
	 * @var BaseApp $appInstance
	 */
	private static $appInstance = null;
	
	/**
	 * BaseApp constructor.
	 */
	public function __construct() {
		parent::__construct();
		if (self::$appInstance === null) {
			self::$appInstance = $this;
		}
	}

	/**
	 * Returns the application instance.
	 * @return BaseApp
	 */
	public static function getInstance() {
		return self::$appInstance;
	}

	/**
	 * @var string $_entryPath
	 */
	private $_entryPath;
	
	/**
	 * Path to global entry point.
	 * @return string
	 */
	protected function getEntryPath() {
		return $this->_entryPath;
	}

	/**
	 * @var string $_path
	 */
	private $_path;

	/**
	 * Gets the path of application class.
	 * @return string
	 */
	protected function getPath() {
		if ($this->_path === null) {
			$appClass = new \ReflectionClass(get_called_class());
			$this->_path = unixPath(dirname($appClass->getFileName()));
		}
		
		return $this->_path;
	}

	/**
	 * @var array $_config
	 */
	private $_config;

	/**
	 * Gets application configuration settings.
	 * @return array
	 */
	protected function getConfig() {
		if ($this->_config === null) {
			try {
				$this->_config = require($this->path.'/config.php');
			} catch (Exception $ex) {
				// 'Application config not loaded'
				Debug::logException(new CoreException('HTTP_APP_GET_ROUTES_FILE_NOT_LOADED', E_ERROR, $ex));
				$this->_config = [];
			}
		}
		
		return $this->_config;
	}

	/**
	 * Initialises application parameters.
	 */
	protected function initParams() {
		require(__DIR__.'/../functions.php');
		
		$this->_entryPath = unixPath(getcwd());
	}

	protected abstract function init();

	public abstract function run();

}