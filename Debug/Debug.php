<?php
namespace pandora3\core\Debug;

use pandora3\core\Logger\ILogger;
use \Throwable;

class Debug {

	/**
	 * @var ILogger $logger
	 */
	private static $logger;

	/**
	 * @param ILogger $logger
	 */
	public function init(ILogger $logger) {
		self::$logger = $logger;
	}

	/**
	 * @return ILogger
	 */
	public function getLogger() {
		return self::$logger;
	}

	/**
	 * @param Throwable $ex
	 */
	public static function logException(Throwable $ex) {
		$subMessages = [];

		$e = $ex->getPrevious();
		while ($e != null) {
			$subMessages[] = [
				'type' => 'exception',
				'level' => $e->getCode(),
				'message' => $e->getMessage(),
				'params' => ($e instanceof CoreException) ? $e->getParams() : [],
				'file' => $e->getFile(),
				'line' => $e->getLine()
			];
			$e = $e->getPrevious();
		}

		self::$logger->log([
			'type' => 'exception',
			'level' => $ex->getCode(),
			'channels' => ['system'],
			'message' => $ex->getMessage(),
			'params' => ($ex instanceof CoreException) ? $ex->getParams() : [],
			'subMessages' => $subMessages,
			'file' => $ex->getFile(),
			'line' => $ex->getLine()
		]);
	}

	/**
	 * @param $value
	 * @return string
	 */
	public static function dumpValue($value) {
		if (is_string($value)) {
			$dump = '"'.$value.'"';
		} else if ($value === null) {
			$dump = 'null';
		} else {
			ob_start();
			var_dump($value);
			$dump = ob_get_clean();
		}
		return $dump;
	}

	/**
	 * @param $value
	 * @param string $label
	 */
	public static function log($value, string $label = '') {
		$message = self::dumpValue($value);
		self::$logger->log([
			'type' => 'log',
			'channels' => ['debug'],
			'message' => $message,
			'value' => $value,
			'label' => $label,
			'file' => '',
			'line' => ''
		]);
	}

}