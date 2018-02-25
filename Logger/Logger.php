<?php
namespace pandora3\core\Logger;

class Logger implements ILogger {

	/**
	 * @var array $messages
	 */
	private $messages = [];

	/**
	 * @param array $message
	 */
	public function log(array $message) {
		$this->messages[] = $message;
	}

}