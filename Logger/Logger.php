<?php
namespace pandora\core3\Logger;

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