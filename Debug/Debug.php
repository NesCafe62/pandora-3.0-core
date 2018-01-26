<?php
namespace pandora\core3\Debug;

use \Throwable;

class Debug {

	public static function logException(Throwable $e) {
		// $e->getMessage();
		// $e->getFile();
		// $e->getLine();
		// $e->getCode();
	}
	
	/**
	 * @param string $message
	 * @param mixed[] ...$params
	 * @return string
	 */
	public static function errorMessage(string $message, ...$params) {
		$messageText = $message;
		foreach ($params as $param) {
			// todo: string dump
			if (is_string($param)) {
				$paramText = '"'.$param.'"';
			} else if ($param === null) {
				$paramText = 'null';
			} else {
				ob_start();
				var_dump($param);
				$paramText = ob_get_clean();
			}
			$messageText .= ' '.$paramText;
		}
		return $messageText;
	}

}