<?php
namespace pandora3\core\Debug;

use pandora3\core\Logger\{Logger, ILogger};
use \Throwable;

class Debug {

	/**
	 * @var ILogger $logger
	 */
	private static $logger;

	/**
	 * @param ILogger|null $logger
	 */
	public static function init($logger = null) {
		self::$logger = $logger ?? new Logger();
	}

	/**
	 * @return ILogger
	 */
	public static function getLogger(): ILogger {
		return self::$logger;
	}

	/**
	 * @param int $code
	 * @return string
	 */
	public static function getErrorLabel(int $code) {
		$codes = [
			E_ERROR => 'Error',
			E_WARNING => 'Warning',
			E_PARSE => 'Parse error',
			E_NOTICE => 'Notice',
			E_CORE_ERROR => 'Error',
			E_CORE_WARNING => 'Warning',
			E_USER_ERROR => 'Error',
			E_COMPILE_ERROR => 'Error',
			E_COMPILE_WARNING => 'Warning',
			E_USER_WARNING => 'Warning',
			E_USER_NOTICE => 'Notice',
			E_STRICT => 'Strict',
			E_RECOVERABLE_ERROR => 'Error',
			E_DEPRECATED => 'Deprecated',
			E_USER_DEPRECATED => 'Deprecated'
		];
		return $codes[$code] ?? 'Error';
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

		echo '<b>'.self::getErrorLabel($ex->getCode()).'</b>: '.$ex->getMessage().' in <b>'.$ex->getFile().'</b> on line <b>'.$ex->getLine().'</b><br>';

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
	public static function dumpValue($value): string {
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
	public static function log($value, string $label = ''): void {
		$message = self::dumpValue($value);

		echo '<b>Console</b>: '.$label.' '.$message.'<br>';

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