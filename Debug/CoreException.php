<?php
namespace pandora3\core\Debug;

use \Exception;

class CoreException extends Exception {

	/**
	 * @var array $params
	 */
	private $params;

	/**
	 * CoreException constructor.
	 * @param array|string $params
	 * @param int $code
	 * @param Exception|null $previous
	 */
	public function __construct($params, $code = E_WARNING, ?Exception $previous = null) {
		if (is_string($params)) {
			$message = $params;
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
	private static function errorMessage(string $message, ...$params) { // temporary
		$messageText = $message;
		foreach ($params as $param) {
			// todo_: string dump
			$messageText .= ' '.Debug::dumpValue($param);
		}
		return $messageText;
	}

	public function getParams() {
		return $this->params;
	}

}