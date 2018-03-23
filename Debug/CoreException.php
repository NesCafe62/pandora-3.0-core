<?php
namespace pandora3\core\Debug;

use Exception;
use Throwable;

class CoreException extends Exception {

	/**
	 * @var array $params
	 */
	private $params;

	/**
	 * CoreException constructor
	 * @param array|string $params
	 * @param int $code
	 * @param Throwable|null $previous
	 */
	public function __construct($params, $code = E_WARNING, ?Throwable $previous = null) {
		if (is_string($params)) {
			$message = $params;
			$params = [];
		} else {
			// $message = $params[0] ?? '';
			$message = call_user_func_array([__CLASS__, 'errorMessage'], $params);
			// $message .= $this->getTraceAsString();
		}
		parent::__construct($message, $code, $previous);
		$this->params = $params;
	}


	/**
	 * @param string $message
	 * @param mixed[] ...$params
	 * @return string
	 */
	private static function errorMessage(string $message, ...$params): string { // temporary
		$messageText = $message;
		foreach ($params as $param) {
			// todo_: string dump
			$messageText .= ' '.Debug::dumpValue($param);
		}
		return $messageText;
	}

	/**
	 * @return array
	 */
	public function getParams(): array {
		return $this->params;
	}

}