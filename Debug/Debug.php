<?php
namespace pandora3\core\Debug;

use pandora3\core\Logger\{Logger, ILogger};
use Throwable;

class Debug {

	/**
	 * @var ILogger $logger
	 */
	private static $logger;

	/**
	 * @var string $rootPath
	 */
	private static $rootPath;

	/**
	 * @param ILogger|null $logger
	 */
	public static function init($logger = null): void {
		error_reporting(E_ALL);
		ini_set('display_errors','1');

		self::$logger = $logger ?? new Logger();
		self::$rootPath = dirname(trimRight(unixPath(__DIR__), '/pandora3/core/Debug'));
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
	public static function getErrorLabel(int $code): string {
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

	private static function relativePath($filename) {
		return trimLeft($filename, self::$rootPath);
	}

	/**
	 * @param Throwable $ex
	 */
	public static function logException(Throwable $ex): void {
		$subMessages = [];

		echo '<b>'.self::getErrorLabel($ex->getCode()).'</b>: <pre style="display: inline;">'.str_replace('  ', '    ', $ex->getMessage()).'</pre> in <b>'.self::relativePath($ex->getFile()).'</b> on line <b>'.$ex->getLine().'</b><br>';

		$e = $ex->getPrevious();
		while ($e != null) {
			$subMessages[] = [
				'type' => 'exception',
				'level' => $e->getCode(),
				'message' => $e->getMessage(),
				'params' => ($e instanceof CoreException) ? $e->getParams() : [],
				'file' => self::relativePath($e->getFile()),
				'line' => $e->getLine()
			];

			echo '<pre style="display: inline;">    </pre><b>'.self::getErrorLabel($e->getCode()).'</b>: <pre style="display: inline;">'.str_replace('  ', '    ', $e->getMessage()).'</pre> in <b>'.self::relativePath($e->getFile()).'</b> on line <b>'.$e->getLine().'</b><br>';

			$e = $e->getPrevious();
		}

		self::$logger->log([
			'type' => 'exception',
			'level' => $ex->getCode(),
			'channels' => ['system'],
			'message' => $ex->getMessage(),
			'params' => ($ex instanceof CoreException) ? $ex->getParams() : [],
			'subMessages' => $subMessages,
			'file' => self::relativePath($ex->getFile()),
			'line' => $ex->getLine()
		]);
	}

	public static function dumpTrace($level = 1): void {
		$trace = debug_backtrace();
		$rows = '';
		foreach ($trace as $i => $item) {
			if ($i >= $level) {
				$method = $item['class'].'::'.$item['function'];
				$file = self::relativePath($item['file']);
				$line = $item['line'];
				$rows .= '<pre style="display: inline;">    '.$method.'</pre> in <b>'.$file.'</b> on line <b>'.$line.'</b><br>';
			}
		}

		echo '<b>Trace</b>: <br>'.$rows;
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

		echo '<b>Console</b>: '.$label.' <pre style="display: inline;">'.str_replace('  ', '    ', $message).'</pre><br>';

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